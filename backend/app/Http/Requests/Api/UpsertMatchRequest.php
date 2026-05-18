<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpsertMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'team_home' => ['required', 'string', 'max:255'],
            'team_home_code' => ['nullable', 'string', 'size:3'],
            'team_home_flag_url' => ['nullable', 'url', 'max:1000'],
            'team_away' => ['required', 'string', 'max:255'],
            'team_away_code' => ['nullable', 'string', 'size:3'],
            'team_away_flag_url' => ['nullable', 'url', 'max:1000'],
            'score_home' => ['nullable', 'integer', 'min:0', 'max:30'],
            'score_away' => ['nullable', 'integer', 'min:0', 'max:30'],
            'match_date' => ['required', 'date'],
            'match_time' => ['required', 'date_format:H:i'],
            'stadium' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'group_name' => ['nullable', 'string', 'max:50'],
            'phase' => ['required', 'in:group,round_of_16,quarter,semi,final'],
            'status' => ['required', 'in:upcoming,live,finished'],
        ];
    }
}
