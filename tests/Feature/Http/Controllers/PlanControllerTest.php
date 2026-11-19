<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('plans'));

        $response->assertRedirect(route('login'));
    }

    public function test_visible_plans_are_listed_cheapest_first_with_their_features(): void
    {
        Plan::factory()->create(['name' => 'Pro', 'price_in_cents' => 4_900, 'features' => ['Priority payouts', 'Dedicated support']]);
        Plan::factory()->create(['name' => 'Starter', 'price_in_cents' => 900, 'features' => ['Standard payouts']]);

        $response = $this->actingAs(User::factory()->create())->get(route('plans'));

        $response->assertOk();
        $response->assertSeeInOrder(['Starter', '$9.00', 'Standard payouts', 'Pro', '$49.00', 'Priority payouts', 'Dedicated support']);
    }

    public function test_hidden_plans_are_not_shown(): void
    {
        Plan::factory()->inactive()->create(['name' => 'Legacy']);

        $response = $this->actingAs(User::factory()->create())->get(route('plans'));

        $response->assertDontSee('Legacy');
        $response->assertSee('No plans available yet');
    }

    public function test_each_plan_offers_to_ask_support_about_it(): void
    {
        Plan::factory()->create(['name' => 'Pro']);

        $response = $this->actingAs(User::factory()->create())->get(route('plans'));

        $response->assertSee('data-chat-message="Hi! I\'d like to know more about the Pro plan."', false);
    }

    public function test_plan_details_are_escaped(): void
    {
        Plan::factory()->create(['name' => '<script>alert(1)</script>', 'features' => ['<img src=x onerror=alert(2)>']]);

        $response = $this->actingAs(User::factory()->create())->get(route('plans'));

        $response->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
        $response->assertDontSee('<script>alert(1)</script>', false);
        $response->assertDontSee('<img src=x onerror=alert(2)>', false);
    }
}
