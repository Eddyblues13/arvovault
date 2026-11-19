<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetLinkControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_page_renders(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertOk();
        $response->assertSee('Forgot your password?');
    }

    public function test_login_page_links_to_forgot_password(): void
    {
        $response = $this->get(route('login'));

        $response->assertSee(route('password.request'));
    }

    public function test_reset_link_is_emailed_to_an_existing_account(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'jane@example.com']);

        $response = $this->from(route('password.request'))->post(route('password.email'), ['email' => 'jane@example.com']);

        $response->assertRedirect(route('password.request'));
        $response->assertSessionHas('status', "If an account uses that email address, we've sent it a link to reset the password.");
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_unknown_email_gets_the_same_response_without_revealing_it_has_no_account(): void
    {
        Notification::fake();

        $response = $this->from(route('password.request'))->post(route('password.email'), ['email' => 'nobody@example.com']);

        $response->assertRedirect(route('password.request'));
        $response->assertSessionHas('status', "If an account uses that email address, we've sent it a link to reset the password.");
        $response->assertSessionHasNoErrors();
        Notification::assertNothingSent();
    }

    public function test_email_must_be_valid(): void
    {
        $response = $this->post(route('password.email'), ['email' => 'not-an-email']);

        $response->assertSessionHasErrors(['email' => 'The email field must be a valid email address.']);
    }

    public function test_signed_in_user_is_redirected_to_dashboard(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('password.request'));

        $response->assertRedirect(route('dashboard'));
    }
}
