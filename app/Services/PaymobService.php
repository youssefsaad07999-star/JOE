<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaymobService
{
    private string $baseUrl;

    private string $apiKey;

    private int $integrationId;

    private int $iframeId;

    public function __construct()
    {
        $this->baseUrl = config('paymob.base_url');
        $this->apiKey = config('paymob.api_key');
        $this->integrationId = (int) config('paymob.integration_id');
        $this->iframeId = (int) config('paymob.iframe_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Step 1 — Authenticate → get auth token (valid ~1 hour)
    |--------------------------------------------------------------------------
    */
    public function authenticate(): string
    {
        $response = Http::post("{$this->baseUrl}/auth/tokens", [
            'api_key' => $this->apiKey,
        ]);

        $this->assertSuccess($response, 'Paymob authentication failed');

        return $response->json('token');
    }

    /*
    |--------------------------------------------------------------------------
    | Step 2 — Create a Paymob order → get paymob_order_id
    |--------------------------------------------------------------------------
    */
    public function createOrder(string $authToken, int $amountCents, array $items = []): int
    {
        $response = Http::withToken($authToken)
            ->post("{$this->baseUrl}/ecommerce/orders", [
                'delivery_needed' => false,
                'amount_cents' => $amountCents,
                'currency' => config('paymob.currency'),
                'items' => $items,
            ]);

        $this->assertSuccess($response, 'Paymob order creation failed');

        return $response->json('id');
    }

    /*
    |--------------------------------------------------------------------------
    | Step 3 — Get payment key → short-lived token for the iframe
    |--------------------------------------------------------------------------
    */
    public function createPaymentKey(
        string $authToken,
        int $paymobOrderId,
        int $amountCents,
        array $billingData
    ): string {
        $response = Http::withToken($authToken)
            ->post("{$this->baseUrl}/acceptance/payment_keys", [
                'amount_cents' => $amountCents,
                'expiration' => 3600,
                'order_id' => $paymobOrderId,
                'billing_data' => $billingData,
                'currency' => config('paymob.currency'),
                'integration_id' => $this->integrationId,
            ]);

        $this->assertSuccess($response, 'Paymob payment key creation failed');

        return $response->json('token');
    }

    /*
    |--------------------------------------------------------------------------
    | Step 4 — Build the iframe URL the user is redirected to
    |--------------------------------------------------------------------------
    */
    public function iframeUrl(string $paymentKey): string
    {
        return "https://accept.paymob.com/api/acceptance/iframes/{$this->iframeId}?payment_token={$paymentKey}";
    }

    /*
    |--------------------------------------------------------------------------
    | HMAC verification — used for BOTH the webhook callback and return URL
    |
    | Paymob concatenates specific fields alphabetically and hashes with
    | HMAC-SHA512 using your HMAC secret.
    |--------------------------------------------------------------------------
    */
    public function verifyHmac(array $data): bool
    {
        // Fields Paymob signs — order matters, must match Paymob docs exactly
        $fields = [
            'amount_cents',
            'created_at',
            'currency',
            'error_occured',
            'has_parent_transaction',
            'id',
            'integration_id',
            'is_3d_secure',
            'is_auth',
            'is_capture',
            'is_refunded',
            'is_standalone_payment',
            'is_voided',
            'order',
            'owner',
            'pending',
            'source_data.pan',
            'source_data.sub_type',
            'source_data.type',
            'success',
        ];

        $concatenated = '';
        foreach ($fields as $field) {
            // Paymob uses dot notation for nested — flatten manually
            if (str_contains($field, '.')) {
                [$parent, $child] = explode('.', $field, 2);
                $value = $data[$parent][$child] ?? $data[str_replace('.', '_', $field)] ?? '';
            } else {
                $value = $data[$field] ?? '';
            }

            // Booleans must be stringified as "true"/"false"
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            $concatenated .= $value;
        }

        $expected = hash_hmac('sha512', $concatenated, config('paymob.hmac_secret'));

        return hash_equals($expected, $data['hmac'] ?? '');
    }

    /*
    |--------------------------------------------------------------------------
    | Full flow in one call — authenticate → order → payment key → URL
    | Use this in the CheckoutController to keep it clean.
    |--------------------------------------------------------------------------
    */
    public function initiatePayment(
        int $amountCents,
        array $billingData,
        array $items = []
    ): array {
        $authToken = $this->authenticate();
        $paymobOrderId = $this->createOrder($authToken, $amountCents, $items);
        $paymentKey = $this->createPaymentKey($authToken, $paymobOrderId, $amountCents, $billingData);
        $iframeUrl = $this->iframeUrl($paymentKey);

        return [
            'paymob_order_id' => $paymobOrderId,
            'payment_key' => $paymentKey,
            'iframe_url' => $iframeUrl,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Private helpers
    |--------------------------------------------------------------------------
    */
    private function assertSuccess($response, string $message): void
    {
        if ($response->failed()) {
            throw new RuntimeException(
                "{$message}: ".$response->status().' '.$response->body()
            );
        }
    }
}
