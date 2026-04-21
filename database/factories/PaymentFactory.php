<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Expert;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'         => User::factory(),
            'expert_id'       => Expert::factory(),
            'conversation_id' => Conversation::factory(),
            'amount'          => fake()->randomFloat(2, 50, 500),
            'currency'        => 'MAD',
            'status'          => 'pending',
            'provider'        => 'stripe',
        ];
    }

    public function completed(): static
    {
        return $this->state(['status' => 'completed', 'paid_at' => now()]);
    }
}
