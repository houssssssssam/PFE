<?php

namespace Tests\Feature\Api;

use App\Models\Expert;
use App\Models\User;
use Tests\TestCase;

class AdminExpertValidationTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
    }

    public function test_admin_can_list_pending_experts(): void
    {
        Expert::factory()->pending()->count(3)->create();
        Expert::factory()->count(2)->create(); // validated

        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/admin/experts/pending');

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 3);
    }

    public function test_admin_can_validate_expert(): void
    {
        $expert = Expert::factory()->pending()->create();

        $response = $this->actingAs($this->admin)
            ->putJson("/api/v1/admin/experts/{$expert->id}/validate");

        $response->assertStatus(200);
        $this->assertDatabaseHas('experts', ['id' => $expert->id, 'status' => 'validated']);
    }

    public function test_admin_can_reject_expert(): void
    {
        $expert = Expert::factory()->pending()->create();

        $response = $this->actingAs($this->admin)
            ->putJson("/api/v1/admin/experts/{$expert->id}/reject", [
                'reason' => 'Documents invalides',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('experts', ['id' => $expert->id, 'status' => 'rejected']);
    }
}
