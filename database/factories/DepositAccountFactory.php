<?php

namespace Database\Factories;

use App\Models\DepositAccount;
use App\PayoutMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DepositAccount>
 */
class DepositAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'method' => PayoutMethod::CashApp,
            'account_details' => '$arvovault',
            'instructions' => 'Add your account email in the payment note.',
            'is_active' => true,
        ];
    }

    /**
     * Indicate that deposits with this method are switched off.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
