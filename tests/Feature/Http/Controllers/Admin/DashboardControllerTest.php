<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_platform_shows_zero_totals_and_empty_states(): void
    {
        $admin = Admin::factory()->create(['name' => 'Ada Lovelace']);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('totalUsers', 0);
        $response->assertViewHas('balancesHeldInCents', 0);
        $response->assertViewHas('pendingTransactionsCount', 0);
        $response->assertSee('No transactions yet');
        $response->assertSee('No users yet');
        $response->assertDontSee('waiting for review');
        $response->assertSee('Ada Lovelace');
    }

    public function test_totals_combine_every_users_completed_credits_and_withdrawals(): void
    {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();
        Transaction::factory()->for($firstUser)->create(['amount_in_cents' => 50_000]);
        Transaction::factory()->for($secondUser)->create(['amount_in_cents' => 20_000]);
        Transaction::factory()->for($firstUser)->pending()->create(['amount_in_cents' => 9_900]);
        Transaction::factory()->for($firstUser)->failed()->create(['amount_in_cents' => 7_000]);
        Transaction::factory()->for($firstUser)->withdrawal()->create(['amount_in_cents' => 12_000]);
        Transaction::factory()->for($secondUser)->withdrawal()->pending()->create(['amount_in_cents' => 3_000]);
        Transaction::factory()->for($secondUser)->withdrawal()->failed()->create(['amount_in_cents' => 4_000]);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.dashboard'));

        $response->assertViewHas('totalUsers', 2);
        $response->assertViewHas('totalCreditedInCents', 70_000);
        $response->assertViewHas('totalWithdrawnInCents', 12_000);
        $response->assertViewHas('balancesHeldInCents', 55_000);
        $response->assertSee('$550.00');
    }

    public function test_pending_transactions_prompt_the_admin_to_review_them(): void
    {
        Transaction::factory()->pending()->create();
        Transaction::factory()->withdrawal()->pending()->create();
        Transaction::factory()->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.dashboard'));

        $response->assertViewHas('pendingTransactionsCount', 2);
        $response->assertSee('2 transactions are waiting for review');
        $response->assertSee(route('admin.transactions.index', ['status' => 'pending']));
    }

    public function test_newest_users_are_listed_first_with_their_available_balance(): void
    {
        $olderUser = User::factory()->create(['name' => 'Older Member', 'created_at' => now()->subDays(3)]);
        $newerUser = User::factory()->create(['name' => 'Newer Member', 'created_at' => now()->subDay()]);
        Transaction::factory()->for($newerUser)->create(['amount_in_cents' => 4_200]);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.dashboard'));

        $response->assertSeeInOrder(['Newer Member', '$42.00', 'Older Member', '$0.00']);
        $response->assertSee(route('admin.users.show', $olderUser));
    }
}
