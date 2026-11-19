<?php

namespace Tests\Feature\Http\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class EnsureUserIsNotSuspendedTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['dashboard'])]
    #[TestWith(['wallet'])]
    #[TestWith(['withdraw'])]
    #[TestWith(['deposit'])]
    public function test_suspended_user_is_signed_out_and_told_why(string $routeName): void
    {
        $user = User::factory()->suspended()->create();

        $response = $this->actingAs($user)->get(route($routeName));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email' => 'Your account has been suspended. Contact support if you think this is a mistake.']);
        $this->assertGuest('web');
    }

    public function test_suspended_user_cannot_request_a_withdrawal(): void
    {
        $user = User::factory()->suspended()->create();

        $response = $this->actingAs($user)->post(route('withdraw.store'), [
            'method' => 'paypal',
            'destination' => 'jane@example.com',
            'amount' => '10',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_background_requests_from_a_suspended_user_receive_403(): void
    {
        $user = User::factory()->suspended()->create();

        $response = $this->actingAs($user)->get(route('support.messages.index'), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertForbidden();
        $this->assertGuest('web');
    }

    public function test_active_user_is_let_through(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('dashboard'));

        $response->assertOk();
    }
}
