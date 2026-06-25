<?php

use App\Models\ProductModels\ProductVariant;
use App\Models\User;

describe('Cart Store', function () {

    it('adds a product variant to the cart for a guest user', function () {

        $variant = ProductVariant::factory()->state(['is_active' => true, 'stock_quantity' => 10])->create();

        $response = $this->post(route('cart.store'), [
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $response->assertRedirect();

        $currentSessionId = session()->getId();

        $this->assertDatabaseHas('cart_items', [
            'user_id' => null,
            'session_id' => $currentSessionId,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);
    });

    it('adds an product variant to the cart for an authenticated user', function () {

        $user = User::factory()->create();

        $variant = ProductVariant::factory()->state(['is_active' => true, 'stock_quantity' => 10])->create();

        $response = $this->actingAs($user)->post(route('cart.store'), [
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'session_id' => null,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);
    });

    it('increments quantity when the same variant is added twice', function () {
        $user = User::factory()->create();

        $variant = ProductVariant::factory()->state(['is_active' => true, 'stock_quantity' => 10])->create();

        $this->actingAs($user)->post(route('cart.store'), [
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        $this->actingAs($user)->post(route('cart.store'), [
            'product_variant_id' => $variant->id,
            'quantity' => 3,
        ]);

        $this->assertDatabaseCount('cart_items', 1);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 5, // 2 + 3 = 5
        ]);
    });

    it('fails when requested quantity exceeds stock availability', function () {
        $user = User::factory()->create();

        $variant = ProductVariant::factory()->state(['is_active' => true, 'stock_quantity' => 5])->create();

        $response = $this->actingAs($user)->post(route('cart.store'), [
            'product_variant_id' => $variant->id,
            'quantity' => 6,
        ]);

        $response->assertSessionHasErrors(['quantity']);

        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $user->id,
        ]);
    });

    it('fails when given an invalid product variant id', function () {

        $user = User::factory()->create();

        $nonExistentVariantId = 0;

        $response = $this->actingAs($user)->post(route('cart.store'), [
            'product_variant_id' => $nonExistentVariantId,
            'quantity' => 1,
        ]);

        $response->assertStatus(404);
    });

});
