<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\TransactionStatus;
use App\TransactionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class WithdrawalControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('withdraw'));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_request_a_withdrawal(): void
    {
        $response = $this->post(route('withdraw.store'), [
            'method' => 'paypal',
            'destination' => 'jane@example.com',
            'amount' => '10',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_page_shows_the_available_balance_and_the_users_recent_requests(): void
    {
        $user = User::factory()->create();
        Transaction::factory()->for($user)->create(['amount_in_cents' => 20_000]);
        Transaction::factory()->for($user)->withdrawal('Zelle')->pending()->create(['amount_in_cents' => 5_000, 'destination' => 'jane@example.com']);
        Transaction::factory()->withdrawal('PayPal')->create(['destination' => 'someone-else@example.com']);

        $response = $this->actingAs($user)->get(route('withdraw'));

        $response->assertOk();
        $response->assertViewHas('availableBalanceInCents', 15_000);
        $response->assertSee('$150.00');
        $response->assertSee('Withdrawal to Zelle');
        $response->assertDontSee('someone-else@example.com');
    }

    public function test_valid_request_creates_a_pending_withdrawal_that_reserves_the_funds(): void
    {
        $user = User::factory()->create();
        Transaction::factory()->for($user)->create(['amount_in_cents' => 10_000]);

        $response = $this->actingAs($user)->post(route('withdraw.store'), [
            'method' => 'paypal',
            'destination' => 'jane@example.com',
            'amount' => '40.50',
        ]);

        $response->assertRedirect(route('withdraw'));
        $response->assertSessionHas('status', 'Your withdrawal of $40.50 to PayPal is waiting for review.');
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => TransactionType::Withdrawal->value,
            'status' => TransactionStatus::Pending->value,
            'amount_in_cents' => 4_050,
            'method' => 'PayPal',
            'destination' => 'jane@example.com',
        ]);
        $this->assertSame(5_950, $user->availableBalanceInCents());
    }

    /**
     * A valid destination for every payout method, with the label the withdrawal is stored under.
     *
     * @return array<string, array{string, string, string}>
     */
    public static function payoutMethods(): array
    {
        return [
            'cash app' => ['cash_app', '$janedoe', 'Cash App'],
            'zelle' => ['zelle', '(555) 123-4567', 'Zelle'],
            'paypal' => ['paypal', 'jane@example.com', 'PayPal'],
            'crypto' => ['crypto', 'TJRyWwFs9wTFGZg3JbrVriFbNfCug5tDeC', 'Crypto (USDT)'],
        ];
    }

    #[DataProvider('payoutMethods')]
    public function test_each_payout_method_is_stored_with_its_label(string $method, string $destination, string $expectedLabel): void
    {
        $user = User::factory()->create();
        Transaction::factory()->for($user)->create(['amount_in_cents' => 10_000]);

        $this->actingAs($user)->post(route('withdraw.store'), [
            'method' => $method,
            'destination' => $destination,
            'amount' => '10',
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'method' => $expectedLabel,
            'destination' => $destination,
        ]);
    }

    public function test_request_for_more_than_the_available_balance_is_rejected(): void
    {
        $user = User::factory()->create();
        Transaction::factory()->for($user)->create(['amount_in_cents' => 10_000]);

        $response = $this->actingAs($user)->post(route('withdraw.store'), [
            'method' => 'paypal',
            'destination' => 'jane@example.com',
            'amount' => '100.01',
        ]);

        $response->assertSessionHasErrors(['amount' => 'The amount is more than your available balance.']);
        $this->assertSame(0, $user->transactions()->withdrawals()->count());
    }

    public function test_pending_withdrawals_count_against_the_available_balance(): void
    {
        $user = User::factory()->create();
        Transaction::factory()->for($user)->create(['amount_in_cents' => 10_000]);
        Transaction::factory()->for($user)->withdrawal()->pending()->create(['amount_in_cents' => 8_000]);

        $response = $this->actingAs($user)->post(route('withdraw.store'), [
            'method' => 'paypal',
            'destination' => 'jane@example.com',
            'amount' => '30',
        ]);

        $response->assertSessionHasErrors(['amount' => 'The amount is more than your available balance.']);
        $this->assertSame(1, $user->transactions()->withdrawals()->count());
    }

    public function test_destination_that_does_not_suit_the_method_is_rejected_with_instructions(): void
    {
        $user = User::factory()->create();
        Transaction::factory()->for($user)->create(['amount_in_cents' => 10_000]);

        $response = $this->actingAs($user)->post(route('withdraw.store'), [
            'method' => 'paypal',
            'destination' => '$janedoe',
            'amount' => '10',
        ]);

        $response->assertSessionHasErrors(['destination' => 'Enter the email address of your PayPal account.']);
        $this->assertSame(0, $user->transactions()->withdrawals()->count());
    }

    /**
     * Request fields the withdrawal form must refuse, with the message the user sees.
     *
     * @return array<string, array{array<string, string>, string, string}>
     */
    public static function invalidRequests(): array
    {
        return [
            'missing method' => [['method' => ''], 'method', 'The method field is required.'],
            'unknown method' => [['method' => 'venmo'], 'method', 'The selected method is invalid.'],
            'missing destination' => [['destination' => ''], 'destination', 'The destination field is required.'],
            'missing amount' => [['amount' => ''], 'amount', 'The amount field is required.'],
            'amount below the minimum' => [['amount' => '0.99'], 'amount', 'The amount field must be at least 1.'],
            'fraction of a cent' => [['amount' => '10.555'], 'amount', 'The amount field must have 0-2 decimal places.'],
        ];
    }

    /**
     * @param  array<string, string>  $invalidField
     */
    #[DataProvider('invalidRequests')]
    public function test_invalid_requests_are_rejected_without_reserving_funds(array $invalidField, string $field, string $expectedMessage): void
    {
        $user = User::factory()->create();
        Transaction::factory()->for($user)->create(['amount_in_cents' => 10_000]);

        $response = $this->actingAs($user)->post(route('withdraw.store'), [
            'method' => 'paypal',
            'destination' => 'jane@example.com',
            'amount' => '10',
            ...$invalidField,
        ]);

        $response->assertSessionHasErrors([$field => $expectedMessage]);
        $this->assertSame(0, $user->transactions()->withdrawals()->count());
    }

    public function test_extra_fields_cannot_change_how_the_withdrawal_is_recorded(): void
    {
        $user = User::factory()->create();
        Transaction::factory()->for($user)->create(['amount_in_cents' => 10_000]);

        $this->actingAs($user)->post(route('withdraw.store'), [
            'method' => 'paypal',
            'destination' => 'jane@example.com',
            'amount' => '10',
            'type' => TransactionType::Credit->value,
            'status' => TransactionStatus::Completed->value,
            'user_id' => User::factory()->create()->id,
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => TransactionType::Withdrawal->value,
            'status' => TransactionStatus::Pending->value,
            'amount_in_cents' => 1_000,
        ]);
        $this->assertSame(9_000, $user->availableBalanceInCents());
    }

    public function test_user_without_enough_funds_is_told_why_they_cannot_withdraw(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('withdraw'));

        $response->assertOk();
        $response->assertSee('You need at least $1.00 available to request a withdrawal.');
    }
}
