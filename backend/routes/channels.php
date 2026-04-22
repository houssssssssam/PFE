<?php

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Register all private and presence broadcast channels.
| The callback must return true/false for authorization.
|
*/

/**
 * Private channel for a conversation.
 * Only the owner or assigned expert can listen.
 */
Broadcast::channel('conversation.{conversationId}', function (User $user, int $conversationId) {
    $conversation = Conversation::find($conversationId);

    if (! $conversation) {
        return false;
    }

    return $user->id === $conversation->user_id
        || ($conversation->expert && $conversation->expert->user_id === $user->id)
        || $user->isAdmin();
});

/**
 * Private channel for a specific user (notifications, conversation assignments).
 */
Broadcast::channel('user.{userId}', function (User $user, int $userId) {
    return $user->id === $userId;
});

/**
 * Private channel for a specific expert (escalation notifications).
 */
Broadcast::channel('expert.{expertId}', function (User $user, int $expertId) {
    return $user->expert && $user->expert->id === $expertId;
});

/**
 * Presence channel for online experts.
 * Returns user info for presence tracking.
 */
Broadcast::channel('experts.online', function (User $user) {
    if ($user->expert && $user->expert->is_available) {
        return [
            'id'          => $user->id,
            'name'        => $user->name,
            'expert_id'   => $user->expert->id,
            'category_id' => $user->expert->category_id,
        ];
    }

    return false;
});
