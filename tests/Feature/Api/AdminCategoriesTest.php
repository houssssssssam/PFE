<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\User;
use Tests\TestCase;

class AdminCategoriesTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
    }

    public function test_admin_can_create_category(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/categories', [
            'name'        => 'Médecine',
            'slug'        => 'medecine',
            'description' => 'Conseils médicaux',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', ['slug' => 'medecine']);
    }

    public function test_admin_can_update_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)
            ->putJson("/api/v1/admin/categories/{$category->id}", [
                'name' => 'Updated Name',
                'slug' => $category->slug,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Updated Name']);
    }

    public function test_admin_can_delete_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/v1/admin/categories/{$category->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_category_name_is_required(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/categories', [
            'slug' => 'test',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['name']);
    }
}
