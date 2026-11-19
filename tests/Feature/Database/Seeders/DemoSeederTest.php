<?php

namespace Tests\Feature\Database\Seeders;

use App\Models\Admin;
use App\Models\DepositAccount;
use App\Models\Plan;
use App\Models\Tip;
use App\Models\User;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_fills_every_page_with_sample_content(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        Admin::factory()->create(['email' => 'admin@example.com']);

        $this->seed(DemoSeeder::class);

        $this->assertSame(3, Plan::query()->active()->count());
        $this->assertSame(3, Tip::query()->published()->count());
        $this->assertSame(4, DepositAccount::query()->available()->count());
        $this->assertSame(55_000, $user->availableBalanceInCents());
        $this->assertSame(1, $user->bookings()->count());
        $this->assertSame(2, $user->supportMessages()->count());
    }

    public function test_seeder_skips_sample_activity_when_the_test_accounts_are_missing(): void
    {
        $this->seed(DemoSeeder::class);

        $this->assertSame(3, Plan::count());
        $this->assertDatabaseCount('transactions', 0);
    }
}
