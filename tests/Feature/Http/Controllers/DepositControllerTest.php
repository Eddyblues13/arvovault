<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\DepositAccount;
use App\Models\Transaction;
use App\Models\User;
use App\PayoutMethod;
use App\TransactionStatus;
use App\TransactionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DepositControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('deposit'));

        $response->assertRedirect(route('login'));
    }

    public function test_page_shows_where_to_send_money_for_each_available_method(): void
    {
        DepositAccount::factory()->create(['method' => PayoutMethod::CashApp, 'account_details' => '$arvovault', 'instructions' => 'Add your email in the note.']);
        DepositAccount::factory()->create(['method' => PayoutMethod::Zelle, 'account_details' => 'pay@example.com']);
        DepositAccount::factory()->inactive()->create(['method' => PayoutMethod::PayPal, 'account_details' => 'hidden-paypal@example.com']);
        DepositAccount::factory()->create(['method' => PayoutMethod::Crypto, 'account_details' => null]);

        $response = $this->actingAs(User::factory()->create())->get(route('deposit'));

        $response->assertOk();
        $response->assertSeeInOrder(['Cash App', '$arvovault', 'Add your email in the note.', 'Zelle', 'pay@example.com']);
        $response->assertDontSee('hidden-paypal@example.com');
        $response->assertDontSee('Crypto (USDT)');
    }

    public function test_page_explains_when_no_deposit_methods_are_available(): void
    {
        DepositAccount::factory()->inactive()->create();

        $response = $this->actingAs(User::factory()->create())->get(route('deposit'));

        $response->assertOk();
        $response->assertSee("Deposits aren't available yet", false);
    }

    public function test_submitting_a_deposit_creates_a_pending_credit_with_the_payment_reference(): void
    {
        DepositAccount::factory()->create(['method' => PayoutMethod::CashApp]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('deposit.store'), [
            'method' => 'cash_app',
            'amount' => '75.25',
            'reference' => 'Sent from $janedoe',
        ]);

        $response->assertRedirect(route('deposit'));
        $response->assertSessionHas('status', "Your deposit of $75.25 via Cash App was submitted. It's added to your balance once our team confirms it.");
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => TransactionType::Credit->value,
            'status' => TransactionStatus::Pending->value,
            'amount_in_cents' => 7_525,
            'method' => 'Cash App',
            'reference' => 'Sent from $janedoe',
        ]);
        $this->assertSame(0, $user->availableBalanceInCents());
        $this->assertSame(7_525, $user->pendingCreditsInCents());
    }

    public function test_deposit_is_listed_with_its_reference_on_the_page(): void
    {
        DepositAccount::factory()->create();
        $user = User::factory()->create();
        Transaction::factory()->for($user)->pending()->create(['method' => 'Cash App', 'reference' => 'TX-12345']);
        Transaction::factory()->for($user)->create(['description' => 'Welcome bonus']);

        $response = $this->actingAs($user)->get(route('deposit'));

        $response->assertSee('Deposit via Cash App');
        $response->assertSee('Ref TX-12345');
        $response->assertDontSee('Welcome bonus');
    }

    public function test_method_that_is_switched_off_is_rejected(): void
    {
        DepositAccount::factory()->inactive()->create(['method' => PayoutMethod::CashApp]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('deposit.store'), [
            'method' => 'cash_app',
            'amount' => '10',
            'reference' => 'TX-1',
        ]);

        $response->assertSessionHasErrors(['method' => 'Deposits with this method are not available right now.']);
        $this->assertDatabaseCount('transactions', 0);
    }

    /**
     * Deposit fields the form must refuse, with the message the user sees.
     *
     * @return array<string, array{array<string, string>, string, string}>
     */
    public static function invalidDeposits(): array
    {
        return [
            'missing method' => [['method' => ''], 'method', 'The method field is required.'],
            'unknown method' => [['method' => 'venmo'], 'method', 'The selected method is invalid.'],
            'missing amount' => [['amount' => ''], 'amount', 'The amount field is required.'],
            'amount below the minimum' => [['amount' => '0.50'], 'amount', 'The amount field must be at least 1.'],
            'amount over the limit' => [['amount' => '1000000.01'], 'amount', 'The amount field must not be greater than 1000000.'],
            'fraction of a cent' => [['amount' => '10.001'], 'amount', 'The amount field must have 0-2 decimal places.'],
            'missing reference' => [['reference' => ''], 'reference', 'The payment reference field is required.'],
        ];
    }

    /**
     * @param  array<string, string>  $invalidField
     */
    #[DataProvider('invalidDeposits')]
    public function test_invalid_deposits_are_rejected(array $invalidField, string $field, string $expectedMessage): void
    {
        DepositAccount::factory()->create(['method' => PayoutMethod::CashApp]);

        $response = $this->actingAs(User::factory()->create())->post(route('deposit.store'), [
            'method' => 'cash_app',
            'amount' => '10',
            'reference' => 'TX-1',
            ...$invalidField,
        ]);

        $response->assertSessionHasErrors([$field => $expectedMessage]);
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_user_cannot_mark_their_own_deposit_as_completed(): void
    {
        DepositAccount::factory()->create(['method' => PayoutMethod::CashApp]);
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('deposit.store'), [
            'method' => 'cash_app',
            'amount' => '10',
            'reference' => 'TX-1',
            'status' => TransactionStatus::Completed->value,
            'type' => TransactionType::Withdrawal->value,
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => TransactionType::Credit->value,
            'status' => TransactionStatus::Pending->value,
        ]);
        $this->assertSame(0, $user->availableBalanceInCents());
    }
}
