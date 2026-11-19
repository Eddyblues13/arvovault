<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class NewPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_page_is_filled_with_the_email_from_the_link(): void
    {
        $response = $this->get(route('password.reset', ['token' => 'reset-token', 'email' => 'jane@example.com']));

        $response->assertOk();
        $response->assertSee('Choose a new password');
        $response->assertSee('value="jane@example.com"', false);
        $response->assertSee('value="reset-token"', false);
    }

    public function test_valid_link_sets_the_new_password_and_sends_the_user_to_sign_in(): void
    {
        $user = User::factory()->create(['email' => 'jane@example.com']);
        $token = Password::createToken($user);

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'jane@example.com',
            'password' => 'new-secret-password',
            'password_confirmation' => 'new-secret-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status', 'Your password has been reset.');
        $this->assertTrue(Hash::check('new-secret-password', $user->refresh()->password));
    }

    public function test_invalid_token_is_rejected(): void
    {
        $user = User::factory()->create(['email' => 'jane@example.com']);

        $response = $this->post(route('password.store'), [
            'token' => 'made-up-token',
            'email' => 'jane@example.com',
            'password' => 'new-secret-password',
            'password_confirmation' => 'new-secret-password',
        ]);

        $response->assertSessionHasErrors(['email' => 'This password reset token is invalid.']);
        $this->assertTrue(Hash::check('password', $user->refresh()->password));
    }

    public function test_token_cannot_reset_another_accounts_password(): void
    {
        $jane = User::factory()->create(['email' => 'jane@example.com']);
        $john = User::factory()->create(['email' => 'john@example.com']);
        $token = Password::createToken($jane);

        $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'john@example.com',
            'password' => 'new-secret-password',
            'password_confirmation' => 'new-secret-password',
        ]);

        $this->assertTrue(Hash::check('password', $john->refresh()->password));
        $this->assertTrue(Hash::check('password', $jane->refresh()->password));
    }

    public function test_new_password_must_be_confirmed(): void
    {
        $user = User::factory()->create(['email' => 'jane@example.com']);

        $response = $this->post(route('password.store'), [
            'token' => Password::createToken($user),
            'email' => 'jane@example.com',
            'password' => 'new-secret-password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors(['password' => 'The password field confirmation does not match.']);
        $this->assertTrue(Hash::check('password', $user->refresh()->password));
    }
}
