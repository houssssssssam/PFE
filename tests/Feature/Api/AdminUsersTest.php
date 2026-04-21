<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Tests\TestCase;

class AdminUsersTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
    }

    public function test_admin_can_toggle_user_active_status(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->admin)
            ->putJson("/api/v1/admin/users/{$user->id}/toggle");

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_active' => false]);
    }

    public function test_admin_cannot_deactivate_own_account(): void
    {
        $response = $this->actingAs($this->admin)
            ->putJson("/api/v1/admin/users/{$this->admin->id}/toggle");

        $response->assertStatus(422);
    }

    public function test_toggle_nonexistent_user_returns_404(): void
    {
        $response = $this->actingAs($this->admin)
            ->putJson('/api/v1/admin/users/99999/toggle');

        $response->assertStatus(404);
    }

    public function test_admin_can_filter_users_by_role(): void
    {
        User::factory()->count(3)->create(['role' => 'user']);
        User::factory()->count(2)->create(['role' => 'expert']);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/admin/users?role=expert');

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 2);
    }
}
