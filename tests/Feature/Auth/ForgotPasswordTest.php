<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

describe('ForgotPassword Test', function () {

    beforeEach(function () {
        Notification::fake();
    });

    it('requires a valid email address structure during password reset submission', function () {
        // Act: Submit a malformed string instead of a structured email
        $response = $this->post(route('password.email'), [
            'email' => 'not-an-email-address',
        ]);

        // Assert: The validator should catch it, redirect back, and flag the email field
        $response->assertRedirect()
            ->assertSessionHasErrors(['email']);

        Notification::assertNothingSent();
    });

    it('rejects an empty password reset submission', function () {
        // Act: Submit an empty payload
        $response = $this->post(route('password.email'), [
            'email' => '',
        ]);

        // Assert: The field is marked as required, failing validation cleanly
        $response->assertRedirect()
            ->assertSessionHasErrors(['email']);
    });

    it('returns a successful response when accessing the password reset request endpoint', function () {
        $response = $this->get(route('password.request'));

        $response->assertOk();
    });

    it('dispatches a cryptographically secure password reset notification to an existing account', function () {

        $user = User::factory()->create();

        $response = $this->post(route('password.email', [
            'email' => $user->email,
        ]));

        $response->assertRedirect()->assertSessionHas('status', __(Password::RESET_LINK_SENT));

        Notification::assertSentTo($user, ResetPassword::class);
    });

    it('prevents user enumeration vulnerabilities by returning an identical success signature for non-existent records', function () {

        $response = $this->post(route('password.email', [
            'email' => 'hacker-non-existent@email.com',
        ]));

        $response->assertRedirect()->assertSessionHas('status', __(Password::RESET_LINK_SENT));
        Notification::assertNothingSent();
    });

    it('returns a validation error when the internal password broker enforces a token generation cooldown', function () {

        $user = User::factory()->create();

        $this->post(route('password.email', [
            'email' => $user->email,
        ]));

        $newResponse = $this->post(route('password.email', [
            'email' => $user->email,
        ]));

        $newResponse->assertRedirect()->assertSessionHasErrors(['email']);

        Notification::assertSentTo($user, ResetPassword::class);
    });

    it('enforces rate-limiting boundaries to throttle consecutive brute-force authentication requests', function () {
        $payload = ['email' => 'flood_target@example.com'];

        // here we added at the route middleware barrier (throttle:3,1) to tell him just 3 requests max per minute
        for ($i = 0; $i < 20; $i++) {
            $response = $this->post(route('password.email'), $payload);
        }

        $response->assertStatus(429);
    });
});
