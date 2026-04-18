<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class TwoFactorVerifyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'two_factor_token' => ['required', 'string'],
            'code'             => ['required', 'string', 'size:6', 'regex:/^\d{6}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'two_factor_token.required' => 'Le token 2FA est obligatoire.',
            'code.required'             => 'Le code 2FA est obligatoire.',
            'code.size'                 => 'Le code 2FA doit contenir 6 chiffres.',
            'code.regex'                => 'Le code 2FA doit contenir uniquement des chiffres.',
        ];
    }
}
