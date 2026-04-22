<?php

namespace App\Http\Requests\Api\V1\Expert;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpertProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bio'            => ['nullable', 'string', 'max:2000'],
            'certifications' => ['nullable', 'array'],
            'hourly_rate'    => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'category_id'    => ['nullable', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists'  => 'La catégorie sélectionnée est invalide.',
            'hourly_rate.numeric' => 'Le tarif horaire doit être un nombre.',
            'bio.max'             => 'La bio ne doit pas dépasser 2000 caractères.',
        ];
    }
}
