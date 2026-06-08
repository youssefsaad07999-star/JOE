<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

uses()->group('auth', 'logout');

describe('Logout Test', function () {

    it('logs out an authenticated user', function (): void {

        $user = User::factory()->create();

        $response = actingAs($user)->post(route('logout'));

        $this->assertGuest();

        $this->assertGuest('web');
    });

    it('redirects to home after logout', function (): void {
        $user = User::factory()->create();

        actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('home'));

    });

    it('guests cannot access logout route', function (): void {
        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
    });

});
