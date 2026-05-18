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
        ];
    }
}
