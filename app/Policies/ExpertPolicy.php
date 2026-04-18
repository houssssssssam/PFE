<?php

namespace App\Policies;

use App\Models\Expert;
use App\Models\User;

class ExpertPolicy
{
    /**
     * Any authenticated user can apply to become an expert.
     */
    public function apply(User $user): bool
    {
        return $user->expert === null;
    }

    /**
     * Only the expert's own user can view/update their profile.
     */
    public function update(User $user, Expert $expert): bool
    {
        return $user->id === $expert->user_id;
    }

    /**
     * Only the expert can manage their own documents.
     */
    public function manageDocuments(User $user, Expert $expert): bool
    {
        return $user->id === $expert->user_id;
    }

    /**
     * Only admins can validate or reject experts.
     */
    public function validate(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only admins can validate or reject experts.
     */
    public function reject(User $user): bool
    {
        return $user->isAdmin();
    }
}
