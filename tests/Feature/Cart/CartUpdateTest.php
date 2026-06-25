<?php

use App\Models\ProductModels\CartItem;
use App\Models\ProductModels\ProductVariant;
use App\Models\User;

describe('Cart Update', function () {

    it('updates the quantity of a cart item', function () {
        $user = User::factory()->create();

        $cartItem = CartItem::factory()->create([
            'user_id' => $user->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)->patch(route('cart.update', $cartItem), [
            'quantity' => 3,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 3,
        ]);

    });

    it('cannot update a cart item belonging to another user', function () {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $cartItemOfUser1 = CartItem::factory()->create([
            'user_id' => $user1->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user2)->patch(route('cart.update', $cartItemOfUser1), [
            'quantity' => 3,
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user1->id,
            'quantity' => 1,
        ]);

    });

    it('rejects quantity above stock quantity', function () {
        $user = User::factory()->create();

        $variant = ProductVariant::factory()->create(['stock_quantity' => 5]);

        $cartItem = CartItem::factory()->create([
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)->patch(route('cart.update', $cartItem), [
            'quantity' => 6,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['quantity']);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_variant_id' => $cartItem->variant->id,
            'quantity' => 1,
        ]);
    });

});
