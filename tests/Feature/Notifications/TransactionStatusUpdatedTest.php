<?php

namespace Tests\Feature\Notifications;

use App\Models\Transaction;
use App\Models\User;
use App\Notifications\TransactionStatusUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TransactionStatusUpdatedTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Each review outcome, with the sentence the user reads.
     *
     * @return array<string, array{string, string, ?string, string}>
     */
    public static function outcomes(): array
    {
        return [
            'approved credit' => ['credit', 'completed', null, 'Your credit of $25.00 was approved and added to your balance.'],
            'declined credit' => ['credit', 'failed', null, 'Your credit of $25.00 was declined.'],
            'approved deposit' => ['credit', 'completed', 'Cash App', 'Your deposit of $25.00 via Cash App was approved and added to your balance.'],
            'declined deposit' => ['credit', 'failed', 'Cash App', 'Your deposit of $25.00 via Cash App was declined.'],
            'approved withdrawal' => ['withdrawal', 'completed', 'PayPal', 'Your withdrawal of $25.00 to PayPal was approved.'],
            'declined withdrawal' => ['withdrawal', 'failed', 'PayPal', 'Your withdrawal of $25.00 to PayPal was declined, and the funds are back in your balance.'],
            'approved withdrawal without a method' => ['withdrawal', 'completed', null, 'Your withdrawal of $25.00 was approved.'],
        ];
    }

    #[DataProvider('outcomes')]
    public function test_notification_describes_the_outcome(string $type, string $status, ?string $method, string $expectedMessage): void
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->for($user)->create([
            'type' => $type,
            'status' => $status,
            'method' => $method,
            'amount_in_cents' => 2_500,
        ]);

        $user->notify(new TransactionStatusUpdated($transaction));

        $notification = $user->notifications()->sole();
        $this->assertSame($expectedMessage, $notification->data['message']);
        $this->assertSame(route('wallet'), $notification->data['url']);
    }
}
