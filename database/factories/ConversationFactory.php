<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConversationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'category_id' => Category::factory(),
            'status'      => 'ai',
            'channel'     => 'ai',
            'title'       => fake()->sentence(4),
        ];
    }

    public function closed(): static
    {
        return $this->state(['status' => 'closed', 'closed_at' => now()]);
    }
}
