<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    /**
     * Any authenticated user can create a conversation.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * User can view if they are the owner or the assigned expert.
     */
    public function view(User $user, Conversation $conversation): bool
    {
        return $user->id === $conversation->user_id
            || ($conversation->expert && $conversation->expert->user_id === $user->id)
            || $user->isAdmin();
    }

    /**
     * Only the conversation owner can escalate.
     */
    public function escalate(User $user, Conversation $conversation): bool
    {
        return $user->id === $conversation->user_id;
    }

    /**
     * Owner or assigned expert can close.
     */
    public function close(User $user, Conversation $conversation): bool
    {
        return $user->id === $conversation->user_id
            || ($conversation->expert && $conversation->expert->user_id === $user->id)
            || $user->isAdmin();
    }

    /**
     * Only the owner can rate, and only when closed.
     */
    public function rate(User $user, Conversation $conversation): bool
    {
        return $user->id === $conversation->user_id
            && $conversation->status->value === 'closed';
    }

    /**
     * Owner or assigned expert can send messages.
     */
    public function sendMessage(User $user, Conversation $conversation): bool
    {
        return $user->id === $conversation->user_id
            || ($conversation->expert && $conversation->expert->user_id === $user->id);
    }
}
