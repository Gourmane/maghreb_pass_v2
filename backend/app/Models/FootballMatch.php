<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FootballMatch extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'team_home',
        'team_home_code',
        'team_home_flag_url',
        'team_away',
        'team_away_code',
        'team_away_flag_url',
        'score_home',
        'score_away',
        'match_date',
        'match_time',
        'stadium',
        'city',
        'group_name',
        'phase',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'score_home' => 'integer',
            'score_away' => 'integer',
            'match_date' => 'date',
        ];
    }
}
