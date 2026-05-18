<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpsertAttractionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description_fr' => ['required', 'string'],
            'description_en' => ['required', 'string'],
            'city' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'entry_price' => ['nullable', 'numeric', 'min:0'],
            'opening_hours' => ['nullable', 'string', 'max:255'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['url', 'max:1000'],
            'photo_files' => ['nullable', 'array', 'max:5'],
            'photo_files.*' => ['image', 'max:2048'],
        ];
    }
}
