<?php

namespace App\Http\Requests\Api\V1\Conversation;

use Illuminate\Foundation\Http\FormRequest;

class SendAudioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'audio' => ['required', 'file', 'mimes:webm,ogg,mp3,m4a,wav,aac', 'max:10240'], // 10MB
        ];
    }

    public function messages(): array
    {
        return [
            'audio.required' => 'Le fichier audio est obligatoire.',
            'audio.mimes'    => 'Le format audio doit être WebM, OGG, MP3, M4A, WAV ou AAC.',
            'audio.max'      => 'Le fichier audio ne doit pas dépasser 10 Mo.',
        ];
    }
}
