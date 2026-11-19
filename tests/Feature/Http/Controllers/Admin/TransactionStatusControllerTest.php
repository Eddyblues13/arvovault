<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\TransactionStatusUpdated;
use App\TransactionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class TransactionStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_approving_a_pending_withdrawal_completes_the_payout_and_tells_the_user(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        Transaction::factory()->for($user)->create(['amount_in_cents' => 10_000]);
        $withdrawal = Transaction::factory()->for($user)->withdrawal()->pending()->create(['amount_in_cents' => 4_000]);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')
            ->from(route('admin.transactions.index'))
            ->patch(route('admin.transactions.status.update', $withdrawal), ['status' => 'completed']);

        $response->assertRedirect(route('admin.transactions.index'));
        $response->assertSessionHas('status', 'Transaction approved.');
        $this->assertSame(TransactionStatus::Completed, $withdrawal->refresh()->status);
        $this->assertSame(4_000, $user->totalWithdrawnInCents());
        $this->assertSame(6_000, $user->availableBalanceInCents());
        Notification::assertSentTo(
            $user,
            TransactionStatusUpdated::class,
            fn (TransactionStatusUpdated $notification): bool => $notification->transaction->status === TransactionStatus::Completed,
        );
    }

    public function test_declining_a_pending_withdrawal_releases_the_reserved_funds(): void
    {
        $user = User::factory()->create();
        Transaction::factory()->for($user)->create(['amount_in_cents' => 10_000]);
        $withdrawal = Transaction::factory()->for($user)->withdrawal()->pending()->create(['amount_in_cents' => 4_000]);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')
            ->from(route('admin.users.show', $user))
            ->patch(route('admin.transactions.status.update', $withdrawal), ['status' => 'failed']);

        $response->assertRedirect(route('admin.users.show', $user));
        $response->assertSessionHas('status', 'Transaction declined.');
        $this->assertSame(TransactionStatus::Failed, $withdrawal->refresh()->status);
        $this->assertSame(10_000, $user->availableBalanceInCents());
    }

    public function test_approving_a_pending_credit_adds_it_to_the_balance(): void
    {
        $user = User::factory()->create();
        $credit = Transaction::factory()->for($user)->pending()->create(['amount_in_cents' => 2_500]);

        $this->actingAs(Admin::factory()->create(), 'admin')
            ->patch(route('admin.transactions.status.update', $credit), ['status' => 'completed']);

        $this->assertSame(TransactionStatus::Completed, $credit->refresh()->status);
        $this->assertSame(2_500, $user->availableBalanceInCents());
    }

    #[TestWith(['completed'])]
    #[TestWith(['failed'])]
    public function test_a_transaction_that_was_already_processed_keeps_its_status(string $currentStatus): void
    {
        Notification::fake();
        $transaction = Transaction::factory()->withdrawal()->create(['status' => $currentStatus]);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')
            ->from(route('admin.transactions.index'))
            ->patch(route('admin.transactions.status.update', $transaction), [
                'status' => $currentStatus === 'completed' ? 'failed' : 'completed',
            ]);

        $response->assertRedirect(route('admin.transactions.index'));
        $response->assertSessionHas('error', 'This transaction has already been processed.');
        $this->assertSame($currentStatus, $transaction->refresh()->status->value);
        Notification::assertNothingSent();
    }

    /**
     * Statuses an admin cannot set, with the message they see.
     *
     * @return array<string, array{string, string}>
     */
    public static function invalidStatuses(): array
    {
        return [
            'missing' => ['', 'The status field is required.'],
            'back to pending' => ['pending', 'The selected status is invalid.'],
            'unknown status' => ['approved', 'The selected status is invalid.'],
        ];
    }

    #[DataProvider('invalidStatuses')]
    public function test_status_must_be_completed_or_failed(string $requestedStatus, string $expectedMessage): void
    {
        $transaction = Transaction::factory()->pending()->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')
            ->patch(route('admin.transactions.status.update', $transaction), ['status' => $requestedStatus]);

        $response->assertSessionHasErrors(['status' => $expectedMessage]);
        $this->assertSame(TransactionStatus::Pending, $transaction->refresh()->status);
    }

    public function test_signed_in_user_cannot_change_a_transaction_status(): void
    {
        $user = User::factory()->create();
        $withdrawal = Transaction::factory()->for($user)->withdrawal()->pending()->create();

        $response = $this->actingAs($user)->patch(route('admin.transactions.status.update', $withdrawal), ['status' => 'completed']);

        $response->assertRedirect(route('admin.login'));
        $this->assertSame(TransactionStatus::Pending, $withdrawal->refresh()->status);
    }
}
