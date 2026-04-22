<?php

namespace Tests\Feature\Api;

use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function test_user_can_register(): void
    {
        Queue::fake();

        $response = $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['id', 'name', 'email', 'role']]);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com', 'role' => 'user']);
        Queue::assertPushed(SendEmailJob::class);
    }

    public function test_register_requires_valid_email(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Test',
            'email'                 => 'not-an-email',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    public function test_register_requires_password_confirmation(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Test',
            'email'                 => 'test@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'wrong',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['password']);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        Queue::fake();

        $data = [
            'name'                  => 'Test',
            'email'                 => 'dupe@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $this->postJson('/api/v1/auth/register', $data);
        $response = $this->postJson('/api/v1/auth/register', $data);

        $response->assertStatus(422)->assertJsonValidationErrors(['email']);
    }
}
