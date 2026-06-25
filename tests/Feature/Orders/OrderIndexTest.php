<?php

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('OrderIndexPage Test', function () {

    it('shows the authenticated users orders along with their payment records', function () {
        // Create an order linked to a COD payment record for this specific user
        $order = Order::factory()
            ->processing()
            ->has(Payment::factory()->pending()->cod())
            ->create(['user_id' => $this->user->id]);
        // dd($order);
        $response = $this->actingAs($this->user)
            ->get(route('orders.index'))
            ->assertOk();

        // Verify the order is passed down to the view collection
        $response->assertViewHas('orders', function ($orders) use ($order) {
            return $orders->contains($order);
        });

        // Verify the payment details (like payment method and status) are rendering on screen
        $response->assertSee('Cash on delivery')
            ->assertSee('Pending');
    });

    it('does not show orders belonging to other users', function () {
        $otherUser = User::factory()->create();

        // Create an isolated order + payment sequence for a completely separate user
        $otherOrder = Order::factory()
            ->processing()
            ->has(Payment::factory()->pending()->cod())
            ->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)
            ->get(route('orders.index'))
            ->assertOk();

        // Verify the view collection does NOT contain the other user's order
        $response->assertViewHas('orders', function ($orders) use ($otherOrder) {
            return ! $orders->contains($otherOrder);
        });

        // Optional extra check: Ensure its ID doesn't render in the HTML text anywhere
        $response->assertDontSee($otherOrder->total_price);
    });

    it('redirects guests to login', function () {
        $this->get(route('orders.index'))
            ->assertRedirect(route('login'));
    });

    it('displays orders sorted by the newest first', function () {
        $oldOrder = Order::factory()
            ->has(Payment::factory())
            ->create([
                'user_id' => $this->user->id,
                'created_at' => now()->subDays(5),
            ]);

        // 2. Create a brand new order (right now)
        $newOrder = Order::factory()
            ->has(Payment::factory())
            ->create([
                'user_id' => $this->user->id,
                'created_at' => now(),
            ]);

        // Assert strict HTML sequence tracking
        $this->actingAs($this->user)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertSeeInOrder([
                $newOrder->id,
                $oldOrder->id,
            ]);
    });

    it('paginates the order history list', function () {
        Order::factory()
            ->count(16)
            ->has(Payment::factory())
            ->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('orders.index'))
            ->assertOk();

        // Assuming your Controller uses standard ->paginate(15)
        $ordersCollection = $response->viewData('orders');
        expect($ordersCollection)->toHaveCount(15);

        // Confirm pagination navigational strings exist on-screen
        $response->assertSee('page=2');
    });

    it('shows a clean empty state message if the user has no order history', function () {
        $this->actingAs($this->user)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertSeeInOrder([
                'No orders yet',
                'Your orders will appear here', // Truncated to avoid line-break mismatches
                'Start Shopping',
            ]);
    });
});
