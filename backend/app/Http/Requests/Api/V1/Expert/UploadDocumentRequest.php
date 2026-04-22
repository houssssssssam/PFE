<?php

namespace App\Http\Requests\Api\V1\Expert;

use App\Enums\DocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'type' => ['required', 'string', Rule::in(array_column(DocumentType::cases(), 'value'))],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Le fichier est obligatoire.',
            'file.mimes'    => 'Le fichier doit être au format PDF, JPG ou PNG.',
            'file.max'      => 'Le fichier ne doit pas dépasser 5 Mo.',
            'type.required' => 'Le type de document est obligatoire.',
            'type.in'       => 'Le type de document est invalide.',
        ];
    }
}
