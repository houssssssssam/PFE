<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class KnowledgeBaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'exists:categories,id'],
            'question'    => ['required', 'string', 'max:1000'],
            'answer'      => ['required', 'string'],
            'keywords'    => ['nullable', 'array'],
            'keywords.*'  => ['string', 'max:100'],
            'language'    => ['required', 'in:fr,ar'],
            'is_active'   => ['boolean'],
        ];
    }
}
