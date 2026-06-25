<?php

use App\Models\ProductModels\CartItem;
use App\Models\ProductModels\ProductVariant;
use App\Models\User;

describe('Guest Cart Merge', function () {

    it('merges guest cart into user cart on login', function () {

        $guestSessionId = session()->getId();

        $variant = ProductVariant::factory()->state(['is_active' => true])->create();
        $user = User::factory()->create(['password' => bcrypt('password')]);

        CartItem::factory()->create([
            'user_id' => null,
            'session_id' => $guestSessionId,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        // Here if we didn't sent the cookie to the new post request
        // so it will act as a new user and
        // session controller (store) will create brand new session id as there is no old one
        $response = $this->withCookie(session()->getName(), $guestSessionId)
            ->post(route('login'), [
                'email' => $user->email,
                'password' => 'password',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);
    });

    it('sums quantities and deletes guest item if the user already has the product in their cart', function () {

        $guestSessionId = session()->getId();

        $user = User::factory()->create(['password' => bcrypt('password')]);

        // Create a variant with plenty of stock (e.g., 10)
        $variant = ProductVariant::factory()->create([
            'is_active' => true,
            'stock_quantity' => 10,
        ]);

        // 1. User already has 2 of this item in their cart
        CartItem::factory()->create([
            'user_id' => $user->id,
            'session_id' => null,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        // 2. Guest has 3 of this item in their temporary session cart
        CartItem::factory()->create([
            'user_id' => null,
            'session_id' => $guestSessionId,
            'product_variant_id' => $variant->id,
            'quantity' => 3,
        ]);

        // Act: Log in passing the guest cookie
        $this->withCookie(session()->getName(), $guestSessionId)
            ->post(route('login'), [
                'email' => $user->email,
                'password' => 'password',
            ])->assertRedirect();

        // Assert: User should now have a total quantity of 5 (2 + 3)
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 5,
        ]);

        // Assert: The temporary guest row was deleted completely
        $this->assertDatabaseMissing('cart_items', [
            'user_id' => null,
            'session_id' => $guestSessionId,
        ]);
    });

    it('caps the merged quantity at the product variant stock limit', function () {
        $guestSessionId = session()->getId();

        $user = User::factory()->create(['password' => bcrypt('password')]);

        // Create a variant with a strict stock limit of 5
        $variant = ProductVariant::factory()->create([
            'is_active' => true,
            'stock_quantity' => 5,
        ]);

        // User has 3 items
        CartItem::factory()->create([
            'user_id' => $user->id,
            'session_id' => null,
            'product_variant_id' => $variant->id,
            'quantity' => 3,
        ]);

        // Guest has 4 items (3 + 4 = 7, which exceeds the stock limit of 5)
        CartItem::factory()->create([
            'user_id' => null,
            'session_id' => $guestSessionId,
            'product_variant_id' => $variant->id,
            'quantity' => 4,
        ]);

        // Act: Log in passing the guest cookie
        $this->withCookie(session()->getName(), $guestSessionId)
            ->post(route('login'), [
                'email' => $user->email,
                'password' => 'password',
            ])->assertRedirect();

        // Assert: The quantity should be strictly capped at the max stock limit (5)
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 5,
        ]);

        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 7, // Should NOT be 7
        ]);

    });

    it('clears the guest session cart after merge', function () {
        $guestSessionId = session()->getId();

        $user = User::factory()->create(['password' => bcrypt('password')]);
        $variant = ProductVariant::factory()->state(['is_active' => true])->create();

        CartItem::factory()->create([
            'user_id' => null,
            'session_id' => $guestSessionId,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        $this->withCookie(session()->getName(), $guestSessionId)
            ->post(route('login'), [
                'email' => $user->email,
                'password' => 'password',
            ])->assertRedirect();

        $this->assertDatabaseMissing('cart_items', [
            'session_id' => $guestSessionId,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'session_id' => null,
            'product_variant_id' => $variant->id,
        ]);
    });
});
