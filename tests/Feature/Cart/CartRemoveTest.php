<?php

use App\Models\ProductModels\CartItem;
use App\Models\User;

describe('Cart Remove Test', function () {

    it('removes an item from the cart', function () {
        $user = User::factory()->create();

        $cartItem = CartItem::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('cart.destroy', $cartItem));

        $response->assertRedirect();

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
        ]);
    });

    it('cannot remove a cart item belonging to another user', function () {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $cartItemOfUser1 = CartItem::factory()->create([
            'user_id' => $user1->id,

        ]);

        $response = $this->actingAs($user2)->delete(route('cart.destroy', $cartItemOfUser1));

        $response->assertStatus(403);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user1->id,
        ]);
    });

    it('clears all items from the cart of the authenticated user', function () {
        $user = User::factory()->create();

        CartItem::factory()->count(4)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('cart.clear'));

        $response->assertRedirect();

        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $user->id,
        ]);
    });
});
