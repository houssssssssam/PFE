<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class TwoFactorCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'size:6', 'regex:/^\d{6}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Le code 2FA est obligatoire.',
            'code.size'     => 'Le code 2FA doit contenir 6 chiffres.',
            'code.regex'    => 'Le code 2FA doit contenir uniquement des chiffres.',
        ];
    }
}
