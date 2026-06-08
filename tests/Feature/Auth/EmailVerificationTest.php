<?php

use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\actingAs;

uses()->group('auth', 'verification');
describe('EmailVerification Test', function () {

    it('shows the email verification notice to unverified users', function () {
        $user = User::factory()->unverified()->create();

        // Act & Assert: Attempting to access the notice route displays the correct security layout
        actingAs($user)
            ->get(route('verification.notice'))
            ->assertOk()
            ->assertViewIs('auth.verify-email');
    });

    it('prevents verified users to home when accessing the verification notice page', function () {
        $user = User::factory()->create();

        $response = actingAs($user)
            ->get(route('verification.notice'));

        $response->assertRedirect(route('home'));
    });

    it('redirects verified users to home when attempting to resend a verification link', function (): void {
        Notification::fake();

        // Arrange: Create a user who is ALREADY verified
        /** @var User $verifiedUser */
        $verifiedUser = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // Act: Attempt to hit the POST route that triggers a link resend
        $response = actingAs($verifiedUser)->post(route('verification.send'));

        // Assert: They must be blocked, redirected to home, and NO notification should be sent
        $response->assertRedirect(route('home'))
            ->assertSessionHas('success', 'You already verified your email');

        Notification::assertNotSentTo($verifiedUser, VerifyEmail::class);
    });

    it('verifies email with a valid signed link', function () {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = actingAs($user)->get($verificationUrl);

        $response->assertRedirect(route('home'))
            ->assertSessionHas('success', 'Email verified successfully!');

        expect($user->fresh()->hasVerifiedEmail())->toBeTrue();

        Notification::assertSentTo([$user], WelcomeNotification::class);
    });

    it('rejects an invalid or tampered verification link', function () {
        $user = User::factory()->unverified()->create();

        $validUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $tamperedUrl = $validUrl.'manipulated-payload';

        $response = actingAs($user)->get($tamperedUrl);

        $response->assertForbidden();
        expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
    });

    it('blocks unverified users from reaching checkout', function () {
        $user = User::factory()->unverified()->create();

        $response = actingAs($user)->get(route('checkout.index'));

        $response->assertRedirect(route('verification.notice'));
    });

    it('resends the verification email', function () {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $response = actingAs($user)
            ->from(route('verification.notice'))
            ->post(route('verification.send'));

        $response->assertRedirect(route('verification.notice'))
            ->assertSessionHas('status', 'verification-link-sent');

        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );
    });

});
