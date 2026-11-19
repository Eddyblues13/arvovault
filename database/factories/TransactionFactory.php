<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use App\TransactionStatus;
use App\TransactionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => TransactionType::Credit,
            'status' => TransactionStatus::Completed,
            'amount_in_cents' => fake()->numberBetween(1_000, 50_000),
            'method' => null,
            'description' => null,
        ];
    }

    /**
     * Indicate that the transaction is a withdrawal.
     */
    public function withdrawal(string $method = 'PayPal'): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => TransactionType::Withdrawal,
            'method' => $method,
        ]);
    }

    /**
     * Indicate that the transaction is still pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TransactionStatus::Pending,
        ]);
    }

    /**
     * Indicate that the transaction failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TransactionStatus::Failed,
        ]);
    }
}
