<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'email'              => 'login@example.com',
            'password'           => Hash::make('Password123!'),
            'email_verified_at'  => now(),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'login@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['token' => ['access_token', 'refresh_token']]]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'email'    => 'user@example.com',
            'password' => Hash::make('correct'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'user@example.com',
            'password' => 'wrong',
        ]);

        $response->assertStatus(401);
    }

    public function test_login_fails_for_unverified_email(): void
    {
        User::factory()->unverified()->create([
            'email'    => 'unverified@example.com',
            'password' => Hash::make('Password123!'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'unverified@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(403);
    }

    public function test_login_fails_for_inactive_user(): void
    {
        User::factory()->create([
            'email'             => 'inactive@example.com',
            'password'          => Hash::make('Password123!'),
            'email_verified_at' => now(),
            'is_active'         => false,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'inactive@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_fetch_profile(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('data.email', $user->email);
    }
}
