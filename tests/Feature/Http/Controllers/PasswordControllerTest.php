<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_change_their_password_with_the_current_one(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'password',
            'password' => 'new-secret-password',
            'password_confirmation' => 'new-secret-password',
        ]);

        $response->assertRedirect(route('settings'));
        $response->assertSessionHas('status', 'Password updated.');
        $this->assertTrue(Hash::check('new-secret-password', $user->refresh()->password));
    }

    public function test_wrong_current_password_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'not-my-password',
            'password' => 'new-secret-password',
            'password_confirmation' => 'new-secret-password',
        ]);

        $response->assertSessionHasErrors(['current_password' => 'The password is incorrect.']);
        $this->assertTrue(Hash::check('password', $user->refresh()->password));
    }

    public function test_new_password_must_be_confirmed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'password',
            'password' => 'new-secret-password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors(['password' => 'The password field confirmation does not match.']);
        $this->assertTrue(Hash::check('password', $user->refresh()->password));
    }

    public function test_new_password_must_be_at_least_eight_characters(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'password',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors(['password' => 'The password field must be at least 8 characters.']);
    }

    public function test_guest_cannot_change_a_password(): void
    {
        $response = $this->put(route('password.update'), [
            'current_password' => 'password',
            'password' => 'new-secret-password',
            'password_confirmation' => 'new-secret-password',
        ]);

        $response->assertRedirect(route('login'));
    }
}
