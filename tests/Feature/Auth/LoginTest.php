<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertAuthenticatedAs;

uses()->group('auth', 'login');

describe('Login test', function () {

    it('renders the login page successfully', function (): void {
        $response = $this->get(route('login'));
        $response->assertOk()
            ->assertViewIs(('auth.login'));
    });

    it('logs in a user with valid credentials', function (): void {
        $password = 'secretpassword123';

        $user = User::factory()->create([
            'email' => 'senior.dev@programming.com',
            'password' => Hash::make($password),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'senior.dev@programming.com',
            'password' => $password,
        ]);

        $response->assertRedirect(route('home'));
        assertAuthenticatedAs($user);

    });

    it('fails authentication with an incorrect password', function (): void {

        User::factory()->create([
            'email' => 'senior.dev@programming.com',
            'password' => Hash::make('correct-password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'senior.dev@programming.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirectBackWithErrors()
            ->assertSessionHasErrors(['password']);
        $this->assertGuest();
    });

    it('fails authentication with unregistered email', function (): void {
        $response = $this->post(route('login'), [
            'email' => 'nonexistent-email@programming.com',
            'password' => 'any-password',
        ]);

        $response->assertRedirectBackWithErrors()
            ->assertSessionHasErrors(['password']);
        $this->assertGuest();
    });

    it('redirects authenticated user away from login page', function (): void {
        $user = User::factory()->create();

        actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('home'));
    });
});
