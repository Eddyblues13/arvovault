<?php

namespace Tests\Feature\Notifications;

use App\Models\Transaction;
use App\Models\User;
use App\Notifications\AccountFunded;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountFundedTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_names_the_amount_and_the_reason(): void
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->for($user)->create(['amount_in_cents' => 15_050, 'description' => 'Welcome bonus']);

        $user->notify(new AccountFunded($transaction));

        $notification = $user->notifications()->sole();
        $this->assertSame('$150.50 was added to your balance: Welcome bonus.', $notification->data['message']);
        $this->assertSame(route('wallet'), $notification->data['url']);
    }

    public function test_notification_without_a_reason_names_only_the_amount(): void
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->for($user)->create(['amount_in_cents' => 2_000, 'description' => null]);

        $user->notify(new AccountFunded($transaction));

        $this->assertSame('$20.00 was added to your balance.', $user->notifications()->sole()->data['message']);
    }
}
