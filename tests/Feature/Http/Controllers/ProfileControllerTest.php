<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('settings'));

        $response->assertRedirect(route('login'));
    }

    public function test_settings_page_is_filled_with_the_users_details(): void
    {
        $user = User::factory()->create(['name' => 'Jane Doe', 'email' => 'jane@example.com']);

        $response = $this->actingAs($user)->get(route('settings'));

        $response->assertOk();
        $response->assertSee('value="Jane Doe"', false);
        $response->assertSee('value="jane@example.com"', false);
    }

    public function test_user_can_update_their_name_and_email(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Jane Smith',
            'email' => 'jane.smith@example.com',
        ]);

        $response->assertRedirect(route('settings'));
        $response->assertSessionHas('status', 'Profile updated.');
        $user->refresh();
        $this->assertSame('Jane Smith', $user->name);
        $this->assertSame('jane.smith@example.com', $user->email);
    }

    public function test_user_can_keep_their_current_email(): void
    {
        $user = User::factory()->create(['email' => 'jane@example.com']);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertSame('Jane Smith', $user->refresh()->name);
    }

    public function test_email_used_by_another_account_is_rejected(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);
        $user = User::factory()->create(['email' => 'jane@example.com']);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'email' => 'taken@example.com',
        ]);

        $response->assertSessionHasErrors(['email' => 'The email has already been taken.']);
        $this->assertSame('jane@example.com', $user->refresh()->email);
    }

    public function test_missing_fields_are_rejected(): void
    {
        $user = User::factory()->create(['name' => 'Jane Doe']);

        $response = $this->actingAs($user)->put(route('profile.update'), []);

        $response->assertSessionHasErrors(['name' => 'The name field is required.', 'email' => 'The email field is required.']);
        $this->assertSame('Jane Doe', $user->refresh()->name);
    }

    public function test_user_cannot_change_protected_fields_through_the_profile_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'suspended_at' => '2020-01-01 00:00:00',
            'password' => 'new-secret-password',
        ]);

        $user->refresh();
        $this->assertNull($user->suspended_at);
        $this->assertTrue(Hash::check('password', $user->password));
    }
}
