<?php

namespace Database\Seeders;

use App\Models\FootballMatch;
use Illuminate\Database\Seeder;

class MatchSeeder extends Seeder
{
    public function run(): void
    {
        $matches = [
            ['Maroc', 'MAR', 'https://flagcdn.com/w320/ma.png', 'Portugal', 'POR', 'https://flagcdn.com/w320/pt.png', null, null, '2030-06-14', '20:00', 'Grand Stade Hassan II', 'Casablanca', 'Groupe A', 'group', 'upcoming'],
            ['Espagne', 'ESP', 'https://flagcdn.com/w320/es.png', 'France', 'FRA', 'https://flagcdn.com/w320/fr.png', null, null, '2030-06-15', '17:00', 'Stade Moulay Abdellah', 'Rabat', 'Groupe B', 'group', 'upcoming'],
            ['Argentine', 'ARG', 'https://flagcdn.com/w320/ar.png', 'Croatie', 'CRO', 'https://flagcdn.com/w320/hr.png', null, null, '2030-06-16', '18:00', 'Grand Stade de Tanger', 'Tanger', 'Groupe C', 'group', 'upcoming'],
            ['Brésil', 'BRA', 'https://flagcdn.com/w320/br.png', 'Sénégal', 'SEN', 'https://flagcdn.com/w320/sn.png', null, null, '2030-06-17', '20:00', 'Grand Stade de Marrakech', 'Marrakech', 'Groupe D', 'group', 'upcoming'],
            ['Allemagne', 'GER', 'https://flagcdn.com/w320/de.png', 'États-Unis', 'USA', 'https://flagcdn.com/w320/us.png', null, null, '2030-06-18', '21:00', 'Grand Stade de Tanger', 'Tanger', 'Groupe A', 'group', 'upcoming'],
            ['Angleterre', 'ENG', 'https://flagcdn.com/w320/gb-eng.png', 'Japon', 'JPN', 'https://flagcdn.com/w320/jp.png', null, null, '2030-06-19', '18:00', 'Stade de Marrakech', 'Marrakech', 'Groupe B', 'group', 'upcoming'],
            ['France', 'FRA', 'https://flagcdn.com/w320/fr.png', 'Portugal', 'POR', 'https://flagcdn.com/w320/pt.png', null, null, '2030-06-20', '17:00', 'Grand Stade Hassan II', 'Casablanca', 'Groupe C', 'group', 'upcoming'],
            ['Brésil', 'BRA', 'https://flagcdn.com/w320/br.png', 'Argentine', 'ARG', 'https://flagcdn.com/w320/ar.png', null, null, '2030-06-21', '20:00', 'Stade Moulay Abdellah', 'Rabat', 'Groupe D', 'group', 'upcoming'],
        ];

        foreach ($matches as [$teamHome, $teamHomeCode, $teamHomeFlag, $teamAway, $teamAwayCode, $teamAwayFlag, $scoreHome, $scoreAway, $date, $time, $stadium, $city, $group, $phase, $status]) {
            FootballMatch::updateOrCreate(
                [
                    'team_home' => $teamHome,
                    'team_away' => $teamAway,
                    'match_date' => $date,
                ],
                [
                    'team_home_code' => $teamHomeCode,
                    'team_home_flag_url' => $teamHomeFlag,
                    'team_away_code' => $teamAwayCode,
                    'team_away_flag_url' => $teamAwayFlag,
                    'score_home' => $scoreHome,
                    'score_away' => $scoreAway,
                    'match_time' => $time,
                    'stadium' => $stadium,
                    'city' => $city,
                    'group_name' => $group,
                    'phase' => $phase,
                    'status' => $status,
                ],
            );
        }
    }
}
