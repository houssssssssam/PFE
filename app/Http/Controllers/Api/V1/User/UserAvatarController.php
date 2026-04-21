<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserAvatarController
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();

        // Delete old avatar from S3 if it exists
        if ($user->avatar_url) {
            $oldPath = str_replace(Storage::disk('s3')->url(''), '', $user->avatar_url);
            Storage::disk('s3')->delete($oldPath);
        }

        $path = $request->file('avatar')->store(
            "avatars/{$user->id}",
            's3'
        );

        $user->update(['avatar_url' => Storage::disk('s3')->url($path)]);

        return response()->json(['data' => new UserResource($user->fresh())]);
    }
}
