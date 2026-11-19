<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('admin:create')]
#[Description('Create an admin account that can sign in to the admin panel')]
class CreateAdmin extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = text(
            label: 'Name',
            required: true,
            validate: ['name' => ['string', 'max:255']],
        );

        $email = text(
            label: 'Email address',
            required: true,
            validate: ['email' => ['string', 'lowercase', 'email', 'max:255', Rule::unique(Admin::class)]],
        );

        $password = password(
            label: 'Password',
            required: true,
            validate: ['password' => [Password::defaults()]],
        );

        password(
            label: 'Confirm password',
            required: true,
            validate: fn (string $confirmation): ?string => $confirmation === $password ? null : 'The passwords do not match.',
        );

        $admin = Admin::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $this->components->info("Admin [{$admin->email}] created. Sign in at ".route('admin.login').'.');

        return self::SUCCESS;
    }
}
