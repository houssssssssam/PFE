<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Requests\Api\V1\User\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserProfileController
{
    public function show(Request $request): JsonResponse
    {
        return response()->json(['data' => new UserResource($request->user())]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $request->user()->update($request->validated());

        return response()->json(['data' => new UserResource($request->user()->fresh())]);
    }
}
