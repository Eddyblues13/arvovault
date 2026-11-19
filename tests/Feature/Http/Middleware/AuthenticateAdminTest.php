<?php

namespace Tests\Feature\Http\Middleware;

use App\Models\Admin;
use App\Models\Booking;
use App\Models\Plan;
use App\Models\Tip;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthenticateAdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Every route that only signed-in admins may use.
     *
     * @return array<string, array{string, string}>
     */
    public static function adminRoutes(): array
    {
        return [
            'dashboard' => ['get', 'admin.dashboard'],
            'users list' => ['get', 'admin.users.index'],
            'user details' => ['get', 'admin.users.show'],
            'edit user' => ['get', 'admin.users.edit'],
            'update user' => ['put', 'admin.users.update'],
            'fund user' => ['post', 'admin.users.funds.store'],
            'transactions list' => ['get', 'admin.transactions.index'],
            'update transaction status' => ['patch', 'admin.transactions.status.update'],
            'support inbox' => ['get', 'admin.support.index'],
            'support conversation' => ['get', 'admin.support.show'],
            'support conversation refresh' => ['get', 'admin.support.messages.index'],
            'support reply' => ['post', 'admin.support.messages.store'],
            'bookings list' => ['get', 'admin.bookings.index'],
            'update booking status' => ['patch', 'admin.bookings.status.update'],
            'plans list' => ['get', 'admin.plans.index'],
            'new plan form' => ['get', 'admin.plans.create'],
            'create plan' => ['post', 'admin.plans.store'],
            'edit plan' => ['get', 'admin.plans.edit'],
            'update plan' => ['put', 'admin.plans.update'],
            'delete plan' => ['delete', 'admin.plans.destroy'],
            'tips list' => ['get', 'admin.tips.index'],
            'new tip form' => ['get', 'admin.tips.create'],
            'create tip' => ['post', 'admin.tips.store'],
            'edit tip' => ['get', 'admin.tips.edit'],
            'update tip' => ['put', 'admin.tips.update'],
            'delete tip' => ['delete', 'admin.tips.destroy'],
            'sign out' => ['post', 'admin.logout'],
        ];
    }

    #[DataProvider('adminRoutes')]
    public function test_guest_is_redirected_to_admin_login(string $method, string $routeName): void
    {
        $response = $this->{$method}($this->urlFor($routeName));

        $response->assertRedirect(route('admin.login'));
    }

    #[DataProvider('adminRoutes')]
    public function test_signed_in_user_is_redirected_to_admin_login(string $method, string $routeName): void
    {
        $response = $this->actingAs(User::factory()->create())->{$method}($this->urlFor($routeName));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_guest_requesting_json_receives_401(): void
    {
        $response = $this->getJson(route('admin.dashboard'));

        $response->assertUnauthorized();
    }

    public function test_signed_in_admin_can_open_admin_pages(): void
    {
        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.dashboard'));

        $response->assertOk();
    }

    /**
     * Build the URL for an admin route, creating any records it needs.
     */
    private function urlFor(string $routeName): string
    {
        return match ($routeName) {
            'admin.users.show',
            'admin.users.edit',
            'admin.users.update',
            'admin.users.funds.store',
            'admin.support.show',
            'admin.support.messages.index',
            'admin.support.messages.store' => route($routeName, User::factory()->create()),
            'admin.transactions.status.update' => route($routeName, Transaction::factory()->pending()->create()),
            'admin.bookings.status.update' => route($routeName, Booking::factory()->create()),
            'admin.plans.edit', 'admin.plans.update', 'admin.plans.destroy' => route($routeName, Plan::factory()->create()),
            'admin.tips.edit', 'admin.tips.update', 'admin.tips.destroy' => route($routeName, Tip::factory()->create()),
            default => route($routeName),
        };
    }
}
