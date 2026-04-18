<?php

namespace App\Http\Requests\Api\V1\Conversation;

use Illuminate\Foundation\Http\FormRequest;

class RateConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'La note est obligatoire.',
            'rating.integer'  => 'La note doit être un nombre entier.',
            'rating.min'      => 'La note minimum est 1.',
            'rating.max'      => 'La note maximum est 5.',
            'comment.max'     => 'Le commentaire ne doit pas dépasser 2000 caractères.',
        ];
    }
}
