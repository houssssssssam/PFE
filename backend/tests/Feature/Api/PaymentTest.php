<?php

namespace Tests\Feature\Api;

use App\Models\Conversation;
use App\Models\Expert;
use App\Models\Payment;
use App\Models\User;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    public function test_authenticated_user_can_view_payment_history(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        Payment::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/v1/payments/history');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'meta']);
    }

    public function test_guest_cannot_access_payment_history(): void
    {
        $response = $this->getJson('/api/v1/payments/history');

        $response->assertStatus(401);
    }

    public function test_admin_can_view_all_payments(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        Payment::factory()->count(5)->create();

        $response = $this->actingAs($admin)->getJson('/api/v1/admin/payments');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'meta', 'stats']);
    }

    public function test_payment_stats_contain_revenue_keys(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        $response = $this->actingAs($admin)->getJson('/api/v1/admin/payments');

        $response->assertStatus(200)
            ->assertJsonStructure(['stats' => ['total_revenue', 'stripe_revenue', 'cmi_revenue', 'pending_count']]);
    }
}
