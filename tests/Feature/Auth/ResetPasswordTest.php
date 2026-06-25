<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

describe('Password Reset Execution Flow', function () {

    it('renders the password modification interface when provided with a valid token', function () {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        // Act: Open the actual link the user would click in their email
        $response = $this->get(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]));

        // Assert: Ensure the interface displays correctly
        $response->assertOk();
    });
    it('successfully updates user credentials in the persistent storage and hashes the new secret', function () {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ]);

        $response->assertRedirect(route('login'))
            ->assertSessionHas('success', __(Password::PASSWORD_RESET));

        expect(Hash::check('123456789', $user->refresh()->password))->toBeTrue();
    });
    it('rejects the modification request if the token is counterfeit or structurally compromised', function () {
        $user = User::factory()->create();

        $response = $this->post(route('password.update'), [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ]);

        $response->assertRedirect()->assertSessionHasErrors(['email']);
    });
    it('enforces structural data constraints to reject mismatched payload confirmation fields', function () {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        // Act: Submit mismatched password parameters
        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'CorrectPassword123!',
            'password_confirmation' => 'TypoMismatchedPassword123!',
        ]);

        // Assert: The core validation layer should catch this immediately
        $response->assertSessionHasErrors(['password']);
    });
    it('revokes cryptographic tokens immediately following a successful credential rotation to prevent replay attacks', function () {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        // Act 1: Use the token to reset the password successfully the first time
        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'FirstReset123!',
            'password_confirmation' => 'FirstReset123!',
        ]);

        // Act 2: Maliciously attempt to execute the exact same payload a second time (Replay Attack)
        $secondResponse = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'HackerPassword123!',
            'password_confirmation' => 'HackerPassword123!',
        ]);

        // Assert: The second attempt must fail completely because the database purged the token upon first use
        $secondResponse->assertSessionHasErrors(['email']);
    });

    it('prevents cross-account hijacking by rejecting a valid token when submitted with a different email address', function () {
        // Arrange: Create two separate user entities
        $victim = User::factory()->create(['email' => 'victim@example.com']);
        $attacker = User::factory()->create(['email' => 'attacker@example.com']);

        // Generate a perfectly valid token... but specifically for the ATTACKER
        $attackerToken = Password::createToken($attacker);

        // Act: Attempt to use the attacker's token to overwrite the victim's password
        $response = $this->post(route('password.update'), [
            'token' => $attackerToken,
            'email' => $victim->email, // Swapping the email target
            'password' => 'HackedPassword2026!',
            'password_confirmation' => 'HackedPassword2026!',
        ]);

        // Assert: The system must identify the mismatch and fail validation cleanly
        $response->assertSessionHasErrors(['email']);

        // Double-check the victim's database record was untouched
        expect(Hash::check('HackedPassword2026!', $victim->refresh()->password))->toBeFalse();
    });

    it('invalidates the password modification attempt if the cryptographic token has exceeded its lifespan', function () {
        // Arrange: Create a user and generate a valid token right now
        $user = User::factory()->create();
        $token = Password::createToken($user);

        // SENIOR TRICK: Fast-forward time by 61 minutes into the future
        $this->travel(61)->minutes();

        // Act: Attempt to use the token now that it has expired
        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'ExpiredTokenReset123!',
            'password_confirmation' => 'ExpiredTokenReset123!',
        ]);

        // Assert: It must reject the attempt due to token expiration
        $response->assertSessionHasErrors(['email']);

        // Ensure time returns back to normal after the assertion so other tests don't break
        $this->travelBack();
    });
});
