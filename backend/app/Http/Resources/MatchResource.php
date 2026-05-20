<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'team_home' => $this->team_home,
            'team_home_code' => $this->team_home_code,
            'team_home_flag_url' => $this->team_home_flag_url,
            'team_away' => $this->team_away,
            'team_away_code' => $this->team_away_code,
            'team_away_flag_url' => $this->team_away_flag_url,
            'score_home' => $this->score_home,
            'score_away' => $this->score_away,
            'match_date' => $this->match_date?->toDateString(),
            'match_time' => $this->match_time,
            'stadium' => $this->stadium,
            'stadium_latitude' => $this->stadium_latitude,
            'stadium_longitude' => $this->stadium_longitude,
            'map_url' => $this->map_url,
            'city' => $this->city,
            'group_name' => $this->group_name,
            'phase' => $this->phase,
            'status' => $this->status,
        ];
    }
}
