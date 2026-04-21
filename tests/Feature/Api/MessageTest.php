<?php

namespace Tests\Feature\Api;

use App\Jobs\ProcessMessageJob;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MessageTest extends TestCase
{
    public function test_user_can_send_message(): void
    {
        Queue::fake();

        $user = User::factory()->create(['email_verified_at' => now()]);
        $conv = Conversation::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson("/api/v1/conversations/{$conv->id}/messages", [
            'content' => 'Hello AI!',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.content', 'Hello AI!');

        Queue::assertPushed(ProcessMessageJob::class);
    }

    public function test_user_can_list_messages(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $conv = Conversation::factory()->create(['user_id' => $user->id]);
        Message::factory()->count(5)->create(['conversation_id' => $conv->id]);

        $response = $this->actingAs($user)->getJson("/api/v1/conversations/{$conv->id}/messages");

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_message_content_is_required(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $conv = Conversation::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson("/api/v1/conversations/{$conv->id}/messages", []);

        $response->assertStatus(422)->assertJsonValidationErrors(['content']);
    }
}
