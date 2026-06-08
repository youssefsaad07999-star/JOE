<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Order;
use App\Models\ProductModels\CartItem;
use App\Models\ShippingMethod;
use App\Models\ShopSetting;
use App\Notifications\OrderCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartTotal = 0;

        $cartItems = CartItem::forUser(Auth::id())
            ->with(['variant.product.images'])
            ->get();

        foreach ($cartItems as $cartItem) {
            $cartTotal += $cartItem->line_total;
        }

        $shipping_methods = ShippingMethod::all();

        $countries = Country::query()->where('is_active', true)->get();

        $free_shipping_threshold = ShopSetting::get('free_shipping_threshold');

        return view('checkout.index', compact(
            'cartItems',
            'cartTotal',
            'shipping_methods',
            'countries',
            'free_shipping_threshold'
        ));
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'first_name' => 'required|string|max:80',
            'last_name' => 'required|string|max:80',
            'address' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'shipping_method' => 'required|exists:shipping_methods,id',
            'payment_method' => 'required|in:card,cod',
        ]);

        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $shippingMethod = ShippingMethod::findOrFail($data['shipping_method']);
        $subtotal = $cartItems->sum(fn ($i) => $i->unit_price * $i->quantity);
        $shippingCost = $this->shippingCost($subtotal, $shippingMethod);
        $total = $subtotal + $shippingCost;

        // Create the local order inside a transaction
        $order = DB::transaction(function () use ($data, $cartItems, $shippingMethod, $shippingCost, $total) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'address_id' => Auth::user()->addresses()->where('is_default', true)->value('id'),
                'shipping_method_id' => $shippingMethod->id,
                'total_price' => $total,
                'status' => 'pending',
                'shipping_first_name' => $data['first_name'],
                'shipping_last_name' => $data['last_name'],
                'shipping_address' => $data['address'],
                'shipping_address2' => $data['address2'] ?? null,
                'shipping_city' => $data['city'],
                'shipping_postal_code' => $data['postal_code'],
                'shipping_phone' => $data['phone'],
                'shipping_method' => $shippingMethod->name,
                'shipping_cost' => $shippingCost,
            ]);

            foreach ($cartItems as $item) {
                $order->variants()->attach($item->product_variant_id, [
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->unit_price * $item->quantity,
                ]);

            }

            return $order;
        });

        // ── Cash on Delivery — no payment gateway ─────────────────────────
        if ($data['payment_method'] === 'cod') {
            $order->update(['status' => 'processing']);

            foreach ($order->variants as $variant) {
                $variant->decrement('stock_quantity', $variant->pivot->quantity);
            }
            $this->clearCart();

            $user = $order->user;
            $user->notify(new OrderCreatedNotification($order));

            return redirect()->route('checkout.success', $order)
                ->with('success', 'Order placed! Pay on delivery.');
        }

        // ── Paddle card checkout ───────────────────────────────────────────
        try {
            $priceId = env('PADDLE_UNIT_PRICE_ID'); // PADDLE_UNIT_PRICE_ID
            $totalCents = (int) round($total * 100);  // $15.50 → 1550

            $checkout = Auth::user()
                ->checkout([$priceId => $totalCents])  // price_id => quantity (cents)
                ->customData([                          // passed to webhook
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                ])
                ->returnTo(route('checkout.success', [$order]));

            return view('checkout.paddle', compact('checkout'));

        } catch (\Throwable $e) {
            $order->update(['status' => 'cancelled']);
            report($e);

            return back()->with('error', 'Could not initiate payment. Please try again.');
        }
    }

    public function success(Order $order)
    {

        abort_if($order->user_id !== Auth::id(), 403);

        // Clear cart on success page load (safe fallback if webhook fires late)
        // $this->clearCart();

        $order->load(['variants.product', 'variants.size', 'variants.color']);

        return view('checkout.success', compact('order'));
    }

    private function clearCart(): void
    {
        CartItem::query()
            ->when(
                Auth::check(),
                fn ($q) => $q->forUser(Auth::id()),
                fn ($q) => $q->forSession(session()->getId())
            )->delete();
    }

    private function getCartItems()
    {
        return CartItem::query()
            ->with(['variant.product.fit', 'variant.size', 'variant.color'])
            ->when(
                Auth::check(),
                fn ($q) => $q->forUser(Auth::id()),
                fn ($q) => $q->forSession(session()->getId())
            )->get();
    }

    private function shippingCost(float $subtotal, ShippingMethod $method): float
    {
        return ($subtotal >= ShopSetting::get('free_shipping_threshold') && $method->name === 'Standard Shipping')
            ? 0
            : $method->price;
    }
}
