<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Notifications\AccountFunded;
use App\TransactionStatus;
use App\TransactionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UserFundControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_funding_adds_a_completed_credit_recorded_against_the_admin(): void
    {
        Notification::fake();
        $user = User::factory()->create(['name' => 'Jane Doe']);
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.users.funds.store', $user), [
            'amount' => '150.50',
            'description' => 'Welcome bonus',
        ]);

        $response->assertRedirect(route('admin.users.show', $user));
        $response->assertSessionHas('status', "Added $150.50 to Jane Doe's balance.");
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'admin_id' => $admin->id,
            'type' => TransactionType::Credit->value,
            'status' => TransactionStatus::Completed->value,
            'amount_in_cents' => 15_050,
            'description' => 'Welcome bonus',
        ]);
        $this->assertSame(15_050, $user->availableBalanceInCents());
        Notification::assertSentTo($user, AccountFunded::class, fn (AccountFunded $notification): bool => $notification->transaction->amount_in_cents === 15_050);
    }

    public function test_extra_fields_cannot_change_how_the_funding_is_recorded(): void
    {
        $user = User::factory()->create();
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')->post(route('admin.users.funds.store', $user), [
            'amount' => '10',
            'type' => TransactionType::Withdrawal->value,
            'status' => TransactionStatus::Pending->value,
            'method' => 'PayPal',
            'user_id' => User::factory()->create()->id,
            'admin_id' => Admin::factory()->create()->id,
        ]);

        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'admin_id' => $admin->id,
            'type' => TransactionType::Credit->value,
            'status' => TransactionStatus::Completed->value,
            'method' => null,
        ]);
    }

    public function test_funding_without_a_description_is_titled_as_a_credit(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->post(route('admin.users.funds.store', $user), [
            'amount' => '20',
        ]);

        $response->assertRedirect(route('admin.users.show', $user));
        $transaction = $user->transactions()->sole();
        $this->assertSame(2_000, $transaction->amount_in_cents);
        $this->assertNull($transaction->description);
        $this->assertSame('Credit', $transaction->title());
    }

    /**
     * Amounts the funding form must refuse, with the message the admin sees.
     *
     * @return array<string, array{string, string}>
     */
    public static function invalidAmounts(): array
    {
        return [
            'missing' => ['', 'The amount field is required.'],
            'not a number' => ['ten dollars', 'The amount field must be a number.'],
            'zero' => ['0', 'The amount field must be at least 0.01.'],
            'negative' => ['-5', 'The amount field must be at least 0.01.'],
            'fraction of a cent' => ['12.345', 'The amount field must have 0-2 decimal places.'],
            'over the limit' => ['1000000.01', 'The amount field must not be greater than 1000000.'],
        ];
    }

    #[DataProvider('invalidAmounts')]
    public function test_invalid_amounts_are_rejected_without_adding_funds(string $amount, string $expectedMessage): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->post(route('admin.users.funds.store', $user), [
            'amount' => $amount,
        ]);

        $response->assertSessionHasErrors(['amount' => $expectedMessage]);
        $this->assertDatabaseCount('transactions', 0);
        Notification::assertNothingSent();
    }

    public function test_largest_allowed_amount_is_accepted(): void
    {
        $user = User::factory()->create();

        $this->actingAs(Admin::factory()->create(), 'admin')->post(route('admin.users.funds.store', $user), [
            'amount' => '1000000',
        ]);

        $this->assertSame(100_000_000, $user->availableBalanceInCents());
    }

    public function test_description_longer_than_255_characters_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->post(route('admin.users.funds.store', $user), [
            'amount' => '10',
            'description' => str_repeat('a', 256),
        ]);

        $response->assertSessionHasErrors(['description' => 'The description field must not be greater than 255 characters.']);
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_signed_in_user_cannot_fund_accounts(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.users.funds.store', $user), [
            'amount' => '500',
        ]);

        $response->assertRedirect(route('admin.login'));
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_funding_a_missing_user_returns_404(): void
    {
        $response = $this->actingAs(Admin::factory()->create(), 'admin')->post(route('admin.users.funds.store', 999), [
            'amount' => '10',
        ]);

        $response->assertNotFound();
        $this->assertDatabaseCount('transactions', 0);
    }
}
