<?php

namespace Tests\Feature\Http\Controllers\Admin\Auth;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticatedSessionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_renders(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertOk();
        $response->assertSee('Admin sign in');
    }

    public function test_login_page_explains_how_to_create_the_first_admin_when_there_is_none(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertSee('No admin accounts exist yet.');
        $response->assertSee('php artisan admin:create');
    }

    public function test_login_page_hides_the_setup_hint_once_an_admin_exists(): void
    {
        Admin::factory()->create();

        $response = $this->get(route('admin.login'));

        $response->assertDontSee('No admin accounts exist yet.');
    }

    public function test_valid_admin_credentials_sign_in_and_redirect_to_admin_dashboard(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin, 'admin');
        $this->assertGuest('web');
    }

    public function test_sign_in_returns_admin_to_the_admin_page_they_originally_requested(): void
    {
        $admin = Admin::factory()->create();

        $this->get(route('admin.users.index'))->assertRedirect(route('admin.login'));

        $response = $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.users.index'));
    }

    public function test_wrong_password_is_rejected(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(['email' => 'These credentials do not match our records.']);
        $this->assertGuest('admin');
    }

    public function test_user_credentials_do_not_sign_in_to_the_admin_area(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route('admin.login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email' => 'These credentials do not match our records.']);
        $this->assertGuest('admin');
        $this->assertGuest('web');
    }

    public function test_admin_login_is_locked_after_five_failed_attempts_even_with_the_correct_password(): void
    {
        $admin = Admin::factory()->create();

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->post(route('admin.login.store'), [
                'email' => $admin->email,
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringStartsWith('Too many login attempts.', session('errors')->first('email'));
        $this->assertGuest('admin');
    }

    public function test_failed_admin_attempts_do_not_lock_the_user_sign_in_for_the_same_email(): void
    {
        $user = User::factory()->create();

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->post(route('admin.login.store'), [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user, 'web');
    }

    public function test_signed_in_admin_can_sign_out_and_is_sent_to_admin_login(): void
    {
        $response = $this->actingAs(Admin::factory()->create(), 'admin')->post(route('admin.logout'));

        $response->assertRedirect(route('admin.login'));
        $this->assertGuest('admin');
    }

    public function test_signed_in_admin_visiting_admin_login_is_redirected_to_admin_dashboard(): void
    {
        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.login'));

        $response->assertRedirect(route('admin.dashboard'));
    }
}
