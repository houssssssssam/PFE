<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SocialTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider' => ['required', 'string', 'in:google,facebook'],
            'token'    => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'provider.in' => 'Le fournisseur doit être google ou facebook.',
            'token.required' => 'Le token d\'accès est obligatoire.',
        ];
    }
}
