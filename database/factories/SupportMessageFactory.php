<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupportMessage>
 */
class SupportMessageFactory extends Factory
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
            'is_from_admin' => false,
            'admin_id' => null,
            'body' => fake()->sentence(),
            'read_at' => null,
        ];
    }

    /**
     * Indicate that a member of the support team wrote the message.
     */
    public function fromAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_from_admin' => true,
            'admin_id' => $attributes['admin_id'] ?? Admin::factory(),
        ]);
    }

    /**
     * Indicate that the other side has read the message.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => now(),
        ]);
    }
}
