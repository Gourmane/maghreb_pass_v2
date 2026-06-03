<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'preferred_language' => ['required', Rule::in(['fr', 'en'])],
            'avatar_url' => ['nullable', 'url', 'max:1000'],
            'avatar_file' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar_url.url' => 'Veuillez saisir une URL valide pour l avatar.',
            'avatar_file.image' => 'Le fichier du profil doit etre une image.',
            'avatar_file.max' => 'L image du profil ne doit pas depasser 2 MB.',
        ];
    }
}
