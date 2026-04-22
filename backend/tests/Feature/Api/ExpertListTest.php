<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Expert;
use App\Models\User;
use Tests\TestCase;

class ExpertListTest extends TestCase
{
    public function test_can_list_validated_experts(): void
    {
        Expert::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/experts');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'meta']);
    }

    public function test_pending_experts_are_not_listed(): void
    {
        Expert::factory()->pending()->create();

        $response = $this->getJson('/api/v1/experts');

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 0);
    }

    public function test_can_filter_experts_by_category(): void
    {
        $category = Category::factory()->create();
        Expert::factory()->create(['category_id' => $category->id]);
        Expert::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/experts?category_id=' . $category->id);

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 1);
    }

    public function test_can_view_expert_detail(): void
    {
        $expert = Expert::factory()->create();

        $response = $this->getJson('/api/v1/experts/' . $expert->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $expert->id);
    }

    public function test_returns_404_for_nonexistent_expert(): void
    {
        $response = $this->getJson('/api/v1/experts/99999');

        $response->assertStatus(404);
    }
}
