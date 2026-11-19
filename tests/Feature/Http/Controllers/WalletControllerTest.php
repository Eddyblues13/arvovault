<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class WalletControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('wallet'));

        $response->assertRedirect(route('login'));
    }

    public function test_wallet_breaks_the_balance_down_into_completed_and_pending_amounts(): void
    {
        $user = User::factory()->create();
        Transaction::factory()->for($user)->create(['amount_in_cents' => 50_000]);
        Transaction::factory()->for($user)->pending()->create(['amount_in_cents' => 9_900]);
        Transaction::factory()->for($user)->failed()->create(['amount_in_cents' => 7_000]);
        Transaction::factory()->for($user)->withdrawal()->create(['amount_in_cents' => 12_000]);
        Transaction::factory()->for($user)->withdrawal()->pending()->create(['amount_in_cents' => 3_000]);
        Transaction::factory()->for($user)->withdrawal()->failed()->create(['amount_in_cents' => 4_000]);

        $response = $this->actingAs($user)->get(route('wallet'));

        $response->assertOk();
        $response->assertViewHas('availableBalanceInCents', 35_000);
        $response->assertViewHas('totalCreditedInCents', 50_000);
        $response->assertViewHas('totalWithdrawnInCents', 12_000);
        $response->assertViewHas('pendingWithdrawalsInCents', 3_000);
        $response->assertViewHas('pendingCreditsInCents', 9_900);
        $response->assertSee('$350.00');
    }

    public function test_history_lists_the_users_transactions_newest_first_and_leaves_out_other_users(): void
    {
        $user = User::factory()->create();
        Transaction::factory()->for($user)->create(['description' => 'Older bonus', 'created_at' => now()->subDays(2)]);
        Transaction::factory()->for($user)->create(['description' => 'Newer bonus', 'created_at' => now()->subDay()]);
        Transaction::factory()->create(['description' => 'Another user bonus']);

        $response = $this->actingAs($user)->get(route('wallet'));

        $response->assertSeeInOrder(['Newer bonus', 'Older bonus']);
        $response->assertDontSee('Another user bonus');
    }

    public function test_history_shows_fifteen_transactions_per_page(): void
    {
        $user = User::factory()->create();
        Transaction::factory()->for($user)->count(16)->create();

        $response = $this->actingAs($user)->get(route('wallet'));

        $response->assertViewHas('transactions', fn (LengthAwarePaginator $transactions): bool => $transactions->count() === 15 && $transactions->total() === 16);
    }

    public function test_type_filter_shows_only_matching_transactions(): void
    {
        $user = User::factory()->create();
        Transaction::factory()->for($user)->create(['description' => 'Welcome bonus']);
        Transaction::factory()->for($user)->withdrawal('Zelle')->create();

        $response = $this->actingAs($user)->get(route('wallet', ['type' => 'withdrawal']));

        $response->assertSee('Withdrawal to Zelle');
        $response->assertDontSee('Welcome bonus');
    }

    public function test_unknown_type_filter_is_ignored(): void
    {
        $user = User::factory()->create();
        Transaction::factory()->for($user)->create(['description' => 'Welcome bonus']);
        Transaction::factory()->for($user)->withdrawal('Zelle')->create();

        $response = $this->actingAs($user)->get(route('wallet', ['type' => 'refund']));

        $response->assertOk();
        $response->assertSee('Welcome bonus');
        $response->assertSee('Withdrawal to Zelle');
    }

    public function test_history_shows_where_each_withdrawal_was_sent(): void
    {
        $user = User::factory()->create();
        Transaction::factory()->for($user)->withdrawal('PayPal')->pending()->create(['destination' => 'jane@example.com']);

        $response = $this->actingAs($user)->get(route('wallet'));

        $response->assertSee('Withdrawal to PayPal');
        $response->assertSee('jane@example.com');
    }
}
