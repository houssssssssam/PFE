<?php

namespace App\Http\Requests\Api\V1\Conversation;

use Illuminate\Foundation\Http\FormRequest;

class CreateReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'conversation_id' => ['required', 'integer', 'exists:conversations,id'],
            'rating'          => ['required', 'integer', 'min:1', 'max:5'],
            'comment'         => ['nullable', 'string', 'max:1000'],
        ];
    }
}
