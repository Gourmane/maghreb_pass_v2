<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertTripItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_type' => ['required', Rule::in(['hotel', 'restaurant', 'attraction', 'match', 'package', 'custom'])],
            'item_id' => ['nullable', 'integer', 'min:1', 'required_unless:item_type,custom'],
            'custom_title' => ['nullable', 'string', 'max:255', 'required_if:item_type,custom'],
            'custom_description' => ['nullable', 'string'],
            'day_number' => ['required', 'integer', 'min:1', 'max:30'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'sort_order' => ['nullable', 'integer', 'min:1', 'max:30'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
