<?php

use App\Models\Country;
use App\Models\ProductModels\CartItem;
use App\Models\ProductModels\ProductVariant;
use App\Models\ShippingMethod;
use App\Models\User;
use App\Notifications\OrderCreatedNotification;
use Database\Seeders\CountrySeeder;
use Database\Seeders\ShippingMethodSeeder;
use Database\Seeders\ShopSettingsSeeder;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();

    $this->seed([
        ShippingMethodSeeder::class,
        ShopSettingsSeeder::class,
        CountrySeeder::class,
    ]);

    $this->user = User::factory()->create();

    $this->shippingMethod = ShippingMethod::first();
    $this->country = Country::where('code', 'EG')->first();

    $this->variant = ProductVariant::factory()->create(['stock_quantity' => 5]);

    $this->cartItem = CartItem::factory()->create([
        'user_id' => $this->user->id,
        'product_variant_id' => $this->variant->id,
        'quantity' => 2,
    ]);

    $this->validOrderData = [
        'email' => $this->user->email,
        'phone' => $this->user->phone_number,
        'first_name' => $this->user->first_name,
        'last_name' => $this->user->last_name,
        'address' => '123 Fashion Street',
        'address2' => 'Apt 4B',
        'city' => 'Alexandria',
        'postal_code' => '10001',
        'country' => $this->country->code,
        'shipping_method' => $this->shippingMethod->id,
        'payment_method' => 'cod',
    ];
});

describe('Place Order Submission', function () {

    // 1. Form validation checks using Pest datasets
    it('requires mandatory form fields', function ($field, $value) {
        $data = $this->validOrderData;
        $data[$field] = $value;

        $this->actingAs($this->user)
            ->post(route('checkout.store'), $data)
            ->assertSessionHasErrors($field);
    })->with([
        ['email', ''],
        ['email', 'invalid-email-format'],
        ['phone', ''],
        ['first_name', ''],
        ['last_name', ''],
        ['address', ''],
        ['city', ''],
        ['postal_code', ''],
        ['country', ''],
        ['shipping_method', ''],
        ['payment_method', 'invalid-method-type'],
    ]);

    // 2. Added: Test for the early exit condition
    it('redirects to the cart index if the cart is completely empty', function () {
        // Clear out the cart item created in beforeEach
        $this->cartItem->delete();

        $this->actingAs($this->user)
            ->post(route('checkout.store'), $this->validOrderData)
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('error', 'Your cart is empty.');

        $this->assertDatabaseCount('orders', 0);
    });

    it('processes order successfully via Cash on Delivery', function () {
        $this->actingAs($this->user)
            ->post(route('checkout.store'), $this->validOrderData)
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'shipping_first_name' => $this->user->first_name,
            'status' => 'processing',
        ]);

        // Deepened Assertion: Verify inventory was actually decremented (5 - 2 = 3)
        $this->assertEquals(3, $this->variant->fresh()->stock_quantity);

        // Verify cart is flushed completely on success
        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $this->user->id,
        ]);

        Notification::assertSentTo($this->user, OrderCreatedNotification::class);
    });

    it('redirects user to Paddle gateway when card method is selected', function () {
        $data = $this->validOrderData;
        $data['payment_method'] = 'card';

        $response = $this->actingAs($this->user)
            ->post(route('checkout.store'), $data);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        // Deepened Assertion: Card payments do NOT decrement inventory until webhook hits
        $this->assertEquals(5, $this->variant->fresh()->stock_quantity);

        $response->assertOk()
            ->assertViewIs('checkout.paddle')
            ->assertViewHas('checkout');
    });

    it('prevents checkout processing and removes item from cart if completely out of stock', function () {
        // Set stock status to exactly zero right before submission
        $this->variant->update(['stock_quantity' => 0]);

        $this->actingAs($this->user)
            ->post(route('checkout.store'), $this->validOrderData)
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('orders', 0);

        // Deepened Assertion: Confirms pass-by-reference cleaned the cart out
        $this->assertDatabaseMissing('cart_items', [
            'id' => $this->cartItem->id,
        ]);
    });

    // 3. Added: Verification that items remain in cart if stock is low but NOT zero
    it('prevents checkout but keeps item in cart if requested quantity exceeds available stock', function () {
        // Update stock to 1. User wants 2 items (insufficient, but not 0).
        $this->variant->update(['stock_quantity' => 1]);

        $this->actingAs($this->user)
            ->post(route('checkout.store'), $this->validOrderData)
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('orders', 0);

        // Critical Check: The item must STILL be in the cart so they can reduce quantity manually
        $this->assertDatabaseHas('cart_items', [
            'id' => $this->cartItem->id,
        ]);
    });

    // 4. Added: Testing Paddle failure resilience
    it('marks the created order as cancelled if the payment gateway initialization throws an exception', function () {
        $data = $this->validOrderData;
        $data['payment_method'] = 'card';

        // 1. Create a partial mock of the user model
        $mockUser = Mockery::mock($this->user)->makePartial();

        // 2. Force the checkout method to throw a definitive exception
        $mockUser->shouldReceive('checkout')
            ->andThrow(new Exception('Paddle API Connection Failed'));

        // 3. Authenticate using the mocked instance and define a "from" page for back() to target
        $this->actingAs($mockUser)
            ->from(route('checkout.index')) // Simulates being on the cart/checkout page
            ->post(route('checkout.store'), $data)
            ->assertRedirect(route('checkout.index'))
            ->assertSessionHas('error', 'Could not initiate payment. Please try again.');

        // 4. Verify the order was safely rolled back to a cancelled status
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'status' => 'cancelled',
        ]);
    });

});
