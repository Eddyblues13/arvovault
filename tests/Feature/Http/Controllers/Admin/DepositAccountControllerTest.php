<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\DepositAccount;
use App\Models\User;
use App\PayoutMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepositAccountControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_lists_every_payment_method_with_its_saved_details(): void
    {
        DepositAccount::factory()->create(['method' => PayoutMethod::Zelle, 'account_details' => 'pay@example.com']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.deposit-accounts.edit'));

        $response->assertOk();
        $response->assertSeeInOrder(['Cash App', 'Zelle', 'PayPal', 'Crypto (USDT)']);
        $response->assertSee('value="pay@example.com"', false);
    }

    public function test_saving_stores_the_details_for_every_method(): void
    {
        $response = $this->actingAs(Admin::factory()->create(), 'admin')->put(route('admin.deposit-accounts.update'), [
            'accounts' => [
                'cash_app' => ['account_details' => '$arvovault', 'instructions' => 'Add your email in the note.', 'is_active' => '1'],
                'zelle' => ['account_details' => 'pay@example.com', 'instructions' => '', 'is_active' => '0'],
                'paypal' => ['account_details' => '', 'instructions' => '', 'is_active' => '0'],
                'crypto' => ['account_details' => '', 'instructions' => '', 'is_active' => '0'],
            ],
        ]);

        $response->assertRedirect(route('admin.deposit-accounts.edit'));
        $response->assertSessionHas('status', 'Deposit methods saved.');
        $this->assertDatabaseCount('deposit_accounts', 4);
        $this->assertDatabaseHas('deposit_accounts', ['method' => 'cash_app', 'account_details' => '$arvovault', 'instructions' => 'Add your email in the note.', 'is_active' => true]);
        $this->assertDatabaseHas('deposit_accounts', ['method' => 'zelle', 'account_details' => 'pay@example.com', 'is_active' => false]);
        $this->assertDatabaseHas('deposit_accounts', ['method' => 'paypal', 'account_details' => null, 'is_active' => false]);
    }

    public function test_saving_again_updates_the_existing_details(): void
    {
        DepositAccount::factory()->create(['method' => PayoutMethod::CashApp, 'account_details' => '$old-tag']);

        $this->actingAs(Admin::factory()->create(), 'admin')->put(route('admin.deposit-accounts.update'), [
            'accounts' => [
                'cash_app' => ['account_details' => '$new-tag', 'is_active' => '1'],
            ],
        ]);

        $this->assertSame('$new-tag', DepositAccount::where('method', 'cash_app')->sole()->account_details);
    }

    public function test_method_cannot_accept_deposits_without_details(): void
    {
        $response = $this->actingAs(Admin::factory()->create(), 'admin')->put(route('admin.deposit-accounts.update'), [
            'accounts' => [
                'cash_app' => ['account_details' => '', 'is_active' => '1'],
            ],
        ]);

        $response->assertSessionHasErrors(['accounts.cash_app.account_details' => 'Enter where users should send the money before accepting deposits with this method.']);
        $this->assertDatabaseCount('deposit_accounts', 0);
    }

    public function test_unknown_payment_methods_are_rejected(): void
    {
        $response = $this->actingAs(Admin::factory()->create(), 'admin')->put(route('admin.deposit-accounts.update'), [
            'accounts' => [
                'venmo' => ['account_details' => '@arvovault', 'is_active' => '1'],
            ],
        ]);

        $response->assertSessionHasErrors('accounts');
        $this->assertDatabaseCount('deposit_accounts', 0);
    }

    public function test_saved_details_appear_on_the_users_add_funds_page(): void
    {
        $this->actingAs(Admin::factory()->create(), 'admin')->put(route('admin.deposit-accounts.update'), [
            'accounts' => [
                'paypal' => ['account_details' => 'payments@example.com', 'is_active' => '1'],
            ],
        ]);

        $response = $this->actingAs(User::factory()->create(), 'web')->get(route('deposit'));

        $response->assertSee('PayPal');
        $response->assertSee('payments@example.com');
    }
}
