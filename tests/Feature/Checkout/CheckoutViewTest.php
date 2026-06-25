<?php

use App\Models\ProductModels\CartItem;
use App\Models\ProductModels\Product;
use App\Models\ProductModels\ProductVariant;
use App\Models\ShippingMethod;
use App\Models\ShopSetting;
use App\Models\User;
use Database\Seeders\CountrySeeder;
use Database\Seeders\ShippingMethodSeeder;
use Database\Seeders\ShopSettingsSeeder;

beforeEach(function () {
    // Only run the seeders specifically needed for checkout rules
    $this->seed([
        ShippingMethodSeeder::class,
        ShopSettingsSeeder::class,
        CountrySeeder::class,
    ]);
});

describe('View Checkout Page', function () {

    it('redirects guests to the login page', function () {
        $this->get(route('checkout.index'))
            ->assertRedirect(route('login'));
    });

    it('redirects to the cart page if the authenticated user has an empty cart', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('checkout.index'))
            ->assertRedirect(route('cart.index'));
    });

    it('renders the checkout page for authenticated users with items in their cart', function () {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->create();

        CartItem::factory()->create([
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)
            ->get(route('checkout.index'))
            ->assertOk()
            ->assertViewIs('checkout.index')
            ->assertSee($user->email);

        ShippingMethod::where('is_active', true)->get()->each(function ($method) use ($response) {
            $response->assertSee($method->name);
        });
    });

    it('displays free shipping when the cart total meets or exceeds the free shipping threshold', function () {
        $user = User::factory()->create();

        $freeShippingThreshold = ShopSetting::get('free_shipping_threshold');

        $product = Product::factory()->create(['base_price' => $freeShippingThreshold]);

        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'price_override' => 0,
        ]);

        CartItem::factory()->create([
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $this->actingAs($user)
            ->get(route('checkout.index'))
            ->assertOk()
            ->assertViewHas('free_shipping_threshold', $freeShippingThreshold)
            ->assertSee('Free');
    });

    it('charges shipping fee when the cart total is below the free shipping threshold', function () {
        $user = User::factory()->create();

        $freeShippingThreshold = ShopSetting::get('free_shipping_threshold');

        $standardShipping = ShippingMethod::where('name', 'Standard Shipping')->first();

        $product = Product::factory()->create(['base_price' => ($freeShippingThreshold - 100)]);

        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'price_override' => 0,
        ]);

        CartItem::factory()->create([
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $this->actingAs($user)
            ->get(route('checkout.index'))
            ->assertOk()
            ->assertViewIs('checkout.index')
            ->assertSeeInOrder([
                '$',
                number_format($standardShipping->price, 2),
            ])
            ->assertSee('cartTotal: '.($product->base_price + $variant->price_override))
            ->assertSee('shippingPrice: '.$standardShipping->price)
            ->assertSee('x-text="(cartTotal + shippingPrice).toFixed(2)"', false);

        // at the view it's calculating the total price
        // as the total price may change if the user choosed another shipping method
    });
});
