<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class ComingSoonPageTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['wallet', 'Wallet'])]
    #[TestWith(['withdraw', 'Withdraw'])]
    #[TestWith(['plans', 'Plans'])]
    #[TestWith(['tips', 'Tips'])]
    #[TestWith(['bookings', 'Bookings'])]
    public function test_signed_in_user_sees_the_placeholder_for_each_sidebar_page(string $routeName, string $pageTitle): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route($routeName));

        $response->assertOk();
        $response->assertSee("<title>{$pageTitle} · ", false);
        $response->assertSee('Coming soon');
    }

    #[TestWith(['wallet'])]
    #[TestWith(['bookings'])]
    public function test_guest_is_redirected_to_login(string $routeName): void
    {
        $response = $this->get(route($routeName));

        $response->assertRedirect(route('login'));
    }
}
