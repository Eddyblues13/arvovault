<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_transactions_from_every_user_are_listed_newest_first(): void
    {
        Transaction::factory()->for(User::factory()->create(['name' => 'Jane Doe']))->create([
            'description' => 'Older bonus',
            'created_at' => now()->subDays(2),
        ]);
        Transaction::factory()->for(User::factory()->create(['name' => 'John Smith']))->create([
            'description' => 'Newer bonus',
            'created_at' => now()->subDay(),
        ]);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.transactions.index'));

        $response->assertOk();
        $response->assertSeeInOrder(['Newer bonus', 'John Smith', 'Older bonus', 'Jane Doe']);
    }

    public function test_status_filter_shows_only_matching_transactions(): void
    {
        Transaction::factory()->pending()->create(['description' => 'Pending bonus']);
        Transaction::factory()->create(['description' => 'Completed bonus']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.transactions.index', ['status' => 'pending']));

        $response->assertSee('Pending bonus');
        $response->assertDontSee('Completed bonus');
    }

    public function test_type_filter_shows_only_matching_transactions(): void
    {
        Transaction::factory()->withdrawal('Zelle')->create();
        Transaction::factory()->create(['description' => 'Welcome bonus']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.transactions.index', ['type' => 'withdrawal']));

        $response->assertSee('Withdrawal to Zelle');
        $response->assertDontSee('Welcome bonus');
    }

    #[TestWith(['status', 'approved'])]
    #[TestWith(['type', 'refund'])]
    public function test_unknown_filter_values_are_ignored(string $filter, string $value): void
    {
        Transaction::factory()->pending()->create(['description' => 'Pending bonus']);
        Transaction::factory()->withdrawal('Zelle')->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.transactions.index', [$filter => $value]));

        $response->assertOk();
        $response->assertSee('Pending bonus');
        $response->assertSee('Withdrawal to Zelle');
    }

    public function test_only_pending_transactions_offer_approve_and_decline_actions(): void
    {
        $pendingTransaction = Transaction::factory()->pending()->create();
        $completedTransaction = Transaction::factory()->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.transactions.index'));

        $response->assertSee(route('admin.transactions.status.update', $pendingTransaction));
        $response->assertDontSee(route('admin.transactions.status.update', $completedTransaction));
    }

    public function test_empty_filter_result_explains_that_nothing_matches(): void
    {
        Transaction::factory()->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.transactions.index', ['status' => 'failed']));

        $response->assertOk();
        $response->assertSee('No transactions found');
        $response->assertSee('Nothing matches these filters right now.');
    }

    public function test_transaction_descriptions_are_escaped(): void
    {
        Transaction::factory()->create(['description' => "<script>alert('xss')</script>"]);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.transactions.index'));

        $response->assertSee('&lt;script&gt;alert(', false);
        $response->assertDontSee("<script>alert('xss')</script>", false);
    }

    public function test_withdrawals_show_where_the_money_must_be_sent(): void
    {
        Transaction::factory()->withdrawal('Cash App')->pending()->create(['destination' => '$janedoe']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.transactions.index'));

        $response->assertSee('Withdrawal to Cash App');
        $response->assertSee('$janedoe');
    }

    public function test_deposits_show_the_payment_reference_to_match_against(): void
    {
        $deposit = Transaction::factory()->pending()->create(['method' => 'Zelle', 'reference' => 'ZL-2026-1120']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.transactions.index'));

        $response->assertSee('Deposit via Zelle');
        $response->assertSee('ZL-2026-1120');
        $response->assertSee(route('admin.transactions.status.update', $deposit));
    }
}
