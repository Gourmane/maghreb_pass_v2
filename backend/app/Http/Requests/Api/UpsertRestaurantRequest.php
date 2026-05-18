<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpsertRestaurantRequest extends FormRequest
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
            'cuisine_type' => ['required', 'string', 'max:255'],
            'price_range' => ['required', 'in:budget,moyen,gastronomique'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['url', 'max:1000'],
            'photo_files' => ['nullable', 'array', 'max:5'],
            'photo_files.*' => ['image', 'max:2048'],
        ];
    }
}
