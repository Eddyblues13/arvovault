<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Booking;
use App\Models\DepositAccount;
use App\Models\Plan;
use App\Models\SupportMessage;
use App\Models\Tip;
use App\Models\Transaction;
use App\Models\User;
use App\PayoutMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Sample content for local development, so every page has something to show.
 *
 * The deposit details are placeholders: replace them in Admin → Deposit methods before going live.
 */
class DemoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the sample content.
     */
    public function run(): void
    {
        $this->seedPlans();
        $this->seedTips();
        $this->seedDepositAccounts();

        $user = User::where('email', 'test@example.com')->first();
        $admin = Admin::where('email', 'admin@example.com')->first();

        if ($user && $admin) {
            $this->seedActivity($user, $admin);
        }
    }

    /**
     * Create the starter plans.
     */
    private function seedPlans(): void
    {
        Plan::factory()->create([
            'name' => 'Starter',
            'price_in_cents' => 0,
            'description' => 'Everything you need to get paid.',
            'features' => ['Standard payouts', 'Email support'],
        ]);

        Plan::factory()->create([
            'name' => 'Plus',
            'price_in_cents' => 1_900,
            'description' => 'Faster payouts for regular earners.',
            'features' => ['Next-day payouts', 'Priority chat support', 'Payout history export'],
        ]);

        Plan::factory()->create([
            'name' => 'Pro',
            'price_in_cents' => 4_900,
            'description' => 'For high-volume accounts.',
            'features' => ['Same-day payouts', 'Dedicated account manager', 'Higher withdrawal limits'],
        ]);
    }

    /**
     * Publish a few starter tips.
     */
    private function seedTips(): void
    {
        Tip::factory()->create([
            'title' => 'Double-check your payout details',
            'body' => "Most delayed payouts come from a typo in the destination. Before you request a withdrawal, confirm your \$Cashtag, email address or wallet address.\n\nFor crypto, make sure your wallet is on the TRON (TRC20) network.",
            'published_at' => now()->subDays(2),
        ]);

        Tip::factory()->create([
            'title' => 'How long do withdrawals take?',
            'body' => 'Our team reviews withdrawals within one business day. You get a notification as soon as yours is approved.',
            'published_at' => now()->subDays(5),
        ]);

        Tip::factory()->create([
            'title' => 'Keep your account secure',
            'body' => 'Use a unique password and never share your sign-in details with anyone, including people who claim to work for us.',
            'published_at' => now()->subDays(8),
        ]);
    }

    /**
     * Turn on placeholder deposit details for every payment method.
     */
    private function seedDepositAccounts(): void
    {
        $placeholders = [
            PayoutMethod::CashApp->value => '$arvovault-demo',
            PayoutMethod::Zelle->value => 'payments@example.com',
            PayoutMethod::PayPal->value => 'payments@example.com',
            PayoutMethod::Crypto->value => 'TJRyWwFs9wTFGZg3JbrVriFbNfCug5tDeC',
        ];

        foreach (PayoutMethod::cases() as $method) {
            DepositAccount::factory()->create([
                'method' => $method,
                'account_details' => $placeholders[$method->value],
                'instructions' => 'Demo details. Replace them in Admin → Deposit methods before going live.',
            ]);
        }
    }

    /**
     * Give the test user a funded balance, a pending withdrawal, a booking and a chat.
     */
    private function seedActivity(User $user, Admin $admin): void
    {
        Transaction::factory()->for($user)->for($admin)->create([
            'amount_in_cents' => 50_000,
            'description' => 'Welcome bonus',
            'created_at' => now()->subDays(6),
        ]);

        Transaction::factory()->for($user)->create([
            'amount_in_cents' => 12_500,
            'method' => PayoutMethod::CashApp->label(),
            'reference' => 'Sent from $testuser',
            'created_at' => now()->subDays(3),
        ]);

        Transaction::factory()->for($user)->withdrawal(PayoutMethod::PayPal->label())->pending()->create([
            'amount_in_cents' => 7_500,
            'destination' => 'test@example.com',
            'created_at' => now()->subHours(5),
        ]);

        Booking::factory()->for($user)->confirmed()->create([
            'topic' => 'Account review',
            'scheduled_for' => now()->addDays(2)->setTime(15, 0),
        ]);

        SupportMessage::factory()->for($user)->read()->create([
            'body' => 'Hi! How long does a PayPal withdrawal take?',
            'created_at' => now()->subHours(4),
        ]);

        SupportMessage::factory()->for($user)->for($admin)->fromAdmin()->create([
            'body' => 'Hi! Usually within one business day. Yours is in review now.',
            'created_at' => now()->subHours(3),
        ]);
    }
}
