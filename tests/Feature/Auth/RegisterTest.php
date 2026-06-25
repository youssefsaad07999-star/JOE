<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\assertDatabaseHas;

uses()->group('auth', 'register');

describe('Register test', function () {

    it('renders the register page successfully', function (): void {
        $response = $this->get(route('register'));
        $response->assertOk()
            ->assertViewIs(('auth.register'));
    });

    it('creates a user with valid data and authenticates him', function (): void {

        $registrationData = [
            'first_name' => 'Joe',
            'last_name' => 'Developer',
            'email' => 'joe.dev@elwekala.com',
            'date_of_birth' => '1995-06-15',
            'phone_number' => '01012345678',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
            'terms' => 'on',
        ];

        $response = $this->post(route('register'), $registrationData);

        $response->assertRedirect(route('verification.notice'));

        assertDatabaseHas('users', [
            'email' => 'joe.dev@elwekala.com',
            'email_verified_at' => null,
        ]);

        $user = User::where('email', 'joe.dev@elwekala.com')->first();

        expect(Hash::check('SecurePassword123!', $user->password))->toBeTrue();

        $this->assertAuthenticatedAs($user);
    });

    it('fails when email already exists in the database', function (): void {
        $existedUser = User::factory()->create();

        $registrationData = [
            'first_name' => 'Joe',
            'last_name' => 'Developer',
            'email' => $existedUser->email,
            'date_of_birth' => '1995-06-15',
            'phone_number' => '01012345678',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
            'terms' => 'on',
        ];

        $response = $this->post(route('register'), $registrationData);

        $response->assertRedirect()
            ->assertSessionHasErrors(['email']);

        $this->assertGuest();
    });

    it('fails when password confirmation does not match', function (): void {
        $response = $this->post(route('register'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@elwekala.com',
            'password' => 'Password123!',
            'password_confirmation' => 'MismatchedPassword999',
            'terms' => 'on',
        ]);

        $response->assertRedirect()
            ->assertSessionHasErrors(['password']);

        $this->assertGuest();
    });

    it('fails registration when mandatory fields are missing', function (string $missingField, array $payload): void {
        $response = $this->post(route('register'), $payload);

        $response->assertRedirect()
            ->assertSessionHasErrors([$missingField]);

        $this->assertGuest();
    })->with([
        'missing first_name' => ['first_name', ['last_name' => 'Doe', 'email' => 'test@elwekala.com', 'password' => 'Pass123!', 'password_confirmation' => 'Pass123!', 'terms' => 'on']],
        'missing last_name' => ['last_name',  ['first_name' => 'John', 'email' => 'test@elwekala.com', 'password' => 'Pass123!', 'password_confirmation' => 'Pass123!', 'terms' => 'on']],
        'missing email' => ['email',      ['first_name' => 'John', 'last_name' => 'Doe', 'password' => 'Pass123!', 'password_confirmation' => 'Pass123!', 'terms' => 'on']],
        'missing password' => ['password',   ['first_name' => 'John', 'last_name' => 'Doe', 'email' => 'test@elwekala.com', 'password_confirmation' => 'Pass123!', 'terms' => 'on']],
        'missing terms' => ['terms',      ['first_name' => 'John', 'last_name' => 'Doe', 'email' => 'test@elwekala.com', 'password' => 'Pass123!', 'password_confirmation' => 'Pass123!']], // terms left blank
    ]);

    it('sends an email verification notification after registration', function (): void {

        Notification::fake();

        $response = $this->post(route('register'), [
            'first_name' => 'Verify',
            'last_name' => 'Me',
            'email' => 'verify@elwekala.com',
            'date_of_birth' => '1995-06-15',
            'phone_number' => '01012345678',
            'password' => 'VerifyPass123!',
            'password_confirmation' => 'VerifyPass123!',
            'terms' => 'on',
        ]);

        $user = User::where('email', 'verify@elwekala.com')->first();

        expect($user)->not()->toBeNull();

        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );

        $response->assertRedirect(route('verification.notice'));

    });

});
