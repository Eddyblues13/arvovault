<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Booking;
use App\Models\Tip;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_new_user_sees_zero_balances_and_empty_activity(): void
    {
        $user = User::factory()->create(['name' => 'Jane Doe']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Welcome back, Jane');
        $response->assertViewHas('availableBalanceInCents', 0);
        $response->assertViewHas('totalCreditedInCents', 0);
        $response->assertViewHas('totalWithdrawnInCents', 0);
        $response->assertSee('$0.00');
        $response->assertSee('No transactions yet');
    }

    public function test_quick_actions_link_to_add_funds_and_withdraw(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('dashboard'));

        $response->assertSee(route('deposit'));
        $response->assertSee(route('withdraw'));
    }

    public function test_dashboard_shows_the_next_booking_that_is_not_cancelled(): void
    {
        $this->travelTo('2026-11-20 12:00:00');
        $user = User::factory()->create();
        Booking::factory()->for($user)->cancelled()->create(['topic' => 'Cancelled session', 'scheduled_for' => '2026-11-21 09:00:00']);
        Booking::factory()->for($user)->confirmed()->create(['topic' => 'Account review', 'scheduled_for' => '2026-11-23 15:00:00']);
        Booking::factory()->for($user)->create(['topic' => 'Later session', 'scheduled_for' => '2026-11-28 10:00:00']);
        Booking::factory()->for($user)->create(['topic' => 'Past session', 'scheduled_for' => '2026-11-19 10:00:00']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertViewHas('nextBooking', fn (?Booking $booking): bool => $booking?->topic === 'Account review');
        $response->assertSee('Account review');
        $response->assertDontSee('Cancelled session');
    }

    public function test_dashboard_lists_the_three_newest_published_tips(): void
    {
        foreach (range(1, 4) as $daysAgo) {
            Tip::factory()->create(['title' => "Tip {$daysAgo}", 'published_at' => now()->subDays($daysAgo)]);
        }
        Tip::factory()->draft()->create(['title' => 'Draft tip']);

        $response = $this->actingAs(User::factory()->create())->get(route('dashboard'));

        $response->assertSeeInOrder(['Tip 1', 'Tip 2', 'Tip 3']);
        $response->assertDontSee('Tip 4');
        $response->assertDontSee('Draft tip');
    }

    public function test_totals_count_completed_credits_and_reserve_pending_withdrawals(): void
    {
        $user = User::factory()->create();
        Transaction::factory()->for($user)->create(['amount_in_cents' => 50_000]);
        Transaction::factory()->for($user)->pending()->create(['amount_in_cents' => 9_900]);
        Transaction::factory()->for($user)->failed()->create(['amount_in_cents' => 7_000]);
        Transaction::factory()->for($user)->withdrawal()->create(['amount_in_cents' => 12_000]);
        Transaction::factory()->for($user)->withdrawal()->pending()->create(['amount_in_cents' => 3_000]);
        Transaction::factory()->for($user)->withdrawal()->failed()->create(['amount_in_cents' => 4_000]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertViewHas('totalCreditedInCents', 50_000);
        $response->assertViewHas('totalWithdrawnInCents', 12_000);
        $response->assertViewHas('availableBalanceInCents', 35_000);
        $response->assertSee('$350.00');
    }

    public function test_dashboard_ignores_transactions_that_belong_to_other_users(): void
    {
        $user = User::factory()->create();
        Transaction::factory()->create(['amount_in_cents' => 90_000, 'description' => 'Another user bonus']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertViewHas('availableBalanceInCents', 0);
        $response->assertDontSee('Another user bonus');
        $response->assertSee('No transactions yet');
    }

    public function test_recent_activity_lists_the_five_newest_transactions_first(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 6) as $daysAgo) {
            Transaction::factory()->for($user)->create([
                'description' => "Deposit {$daysAgo}",
                'created_at' => now()->subDays($daysAgo),
            ]);
        }

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertSeeInOrder(['Deposit 1', 'Deposit 2', 'Deposit 3', 'Deposit 4', 'Deposit 5']);
        $response->assertDontSee('Deposit 6');
    }

    public function test_withdrawal_without_description_is_titled_with_its_payout_method(): void
    {
        $user = User::factory()->create();
        Transaction::factory()->for($user)->withdrawal('Zelle')->create(['amount_in_cents' => 2_500]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertSee('Withdrawal to Zelle');
        $response->assertSee('−$25.00');
    }

    public function test_transaction_descriptions_written_by_admins_are_escaped(): void
    {
        $user = User::factory()->create();
        Transaction::factory()->for($user)->create(['description' => "<script>alert('xss')</script>"]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertSee('&lt;script&gt;alert(', false);
        $response->assertDontSee("<script>alert('xss')</script>", false);
    }
}
