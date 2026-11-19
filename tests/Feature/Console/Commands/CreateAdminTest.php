<?php

namespace Tests\Feature\Console\Commands;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_creates_an_admin_who_can_sign_in(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Name', 'Ada Lovelace')
            ->expectsQuestion('Email address', 'ada@example.com')
            ->expectsQuestion('Password', 'secret-password')
            ->expectsQuestion('Confirm password', 'secret-password')
            ->expectsOutputToContain('Admin [ada@example.com] created.')
            ->assertSuccessful();

        $admin = Admin::where('email', 'ada@example.com')->sole();
        $this->assertSame('Ada Lovelace', $admin->name);
        $this->assertTrue(Hash::check('secret-password', $admin->password));
    }

    public function test_command_rejects_an_email_another_admin_already_uses(): void
    {
        Admin::factory()->create(['email' => 'ada@example.com']);

        $this->artisan('admin:create')
            ->expectsQuestion('Name', 'Ada Lovelace')
            ->expectsQuestion('Email address', 'ada@example.com')
            ->expectsOutputToContain('The email has already been taken.')
            ->assertFailed();

        $this->assertDatabaseCount('admins', 1);
    }

    public function test_command_rejects_an_email_with_uppercase_letters(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Name', 'Ada Lovelace')
            ->expectsQuestion('Email address', 'Ada@Example.com')
            ->expectsOutputToContain('The email field must be lowercase.')
            ->assertFailed();

        $this->assertDatabaseCount('admins', 0);
    }

    public function test_command_rejects_a_password_shorter_than_eight_characters(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Name', 'Ada Lovelace')
            ->expectsQuestion('Email address', 'ada@example.com')
            ->expectsQuestion('Password', 'short')
            ->expectsOutputToContain('The password field must be at least 8 characters.')
            ->assertFailed();

        $this->assertDatabaseCount('admins', 0);
    }

    public function test_command_rejects_a_password_confirmation_that_does_not_match(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Name', 'Ada Lovelace')
            ->expectsQuestion('Email address', 'ada@example.com')
            ->expectsQuestion('Password', 'secret-password')
            ->expectsQuestion('Confirm password', 'different-password')
            ->expectsOutputToContain('The passwords do not match.')
            ->assertFailed();

        $this->assertDatabaseCount('admins', 0);
    }
}
