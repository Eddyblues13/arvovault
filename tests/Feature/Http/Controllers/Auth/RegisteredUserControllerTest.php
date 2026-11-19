<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisteredUserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_renders(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
        $response->assertSee('Create your account');
    }

    public function test_valid_registration_creates_account_signs_in_and_redirects_to_dashboard(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ]);

        $response->assertRedirect(route('dashboard'));

        $user = User::where('email', 'jane@example.com')->sole();
        $this->assertSame('Jane Doe', $user->name);
        $this->assertTrue(Hash::check('secret-password', $user->password));
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_rejects_missing_fields(): void
    {
        $response = $this->post(route('register'), []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertGuest();
    }

    public function test_registration_rejects_an_email_that_is_already_taken(): void
    {
        User::factory()->create(['email' => 'jane@example.com']);

        $response = $this->post(route('register'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ]);

        $response->assertSessionHasErrors(['email' => 'The email has already been taken.']);
        $this->assertGuest();
    }

    public function test_registration_rejects_a_password_confirmation_that_does_not_match(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors(['password' => 'The password field confirmation does not match.']);
        $this->assertDatabaseMissing('users', ['email' => 'jane@example.com']);
    }

    public function test_registration_rejects_a_password_shorter_than_eight_characters(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors(['password' => 'The password field must be at least 8 characters.']);
        $this->assertGuest();
    }

    public function test_signed_in_user_visiting_registration_is_redirected_to_dashboard(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('register'));

        $response->assertRedirect(route('dashboard'));
    }
}
