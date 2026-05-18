<?php

namespace Tests\Feature;

use App\Models\Attraction;
use App\Models\FootballMatch;
use App\Models\Hotel;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCatalogApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_reports_api_status(): void
    {
        $this->getJson('/api/health')
            ->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('version', 'mvp');
    }

    public function test_public_matches_can_be_filtered_by_city(): void
    {
        FootballMatch::create($this->matchPayload([
            'city' => 'Casablanca',
            'team_home' => 'Morocco',
            'team_home_code' => 'MAR',
            'team_home_flag_url' => 'https://flagcdn.com/w320/ma.png',
            'team_away_code' => 'POR',
            'team_away_flag_url' => 'https://flagcdn.com/w320/pt.png',
        ]));
        FootballMatch::create($this->matchPayload(['city' => 'Rabat', 'team_home' => 'Spain']));

        $this->getJson('/api/matches?city=Casablanca')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.city', 'Casablanca')
            ->assertJsonPath('data.0.team_home', 'Morocco')
            ->assertJsonPath('data.0.team_home_code', 'MAR')
            ->assertJsonPath('data.0.team_home_flag_url', 'https://flagcdn.com/w320/ma.png')
            ->assertJsonPath('data.0.team_away_code', 'POR')
            ->assertJsonPath('data.0.team_away_flag_url', 'https://flagcdn.com/w320/pt.png');
    }

    public function test_public_hotels_restaurants_and_attractions_can_be_filtered(): void
    {
        Hotel::create($this->hotelPayload(['city' => 'Marrakech', 'stars' => 5]));
        Hotel::create($this->hotelPayload(['city' => 'Agadir', 'stars' => 4]));

        Restaurant::create($this->restaurantPayload(['city' => 'Rabat', 'cuisine_type' => 'marocaine']));
        Restaurant::create($this->restaurantPayload(['city' => 'Rabat', 'cuisine_type' => 'italienne']));

        Attraction::create($this->attractionPayload(['city' => 'Fes', 'category' => 'historique']));
        Attraction::create($this->attractionPayload(['city' => 'Fes', 'category' => 'jardin']));

        $this->getJson('/api/hotels?city=Marrakech')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.city', 'Marrakech');

        $this->getJson('/api/restaurants?cuisine_type=marocaine')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.cuisine_type', 'marocaine');

        $this->getJson('/api/attractions?category=historique')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.category', 'historique');
    }

    private function matchPayload(array $overrides = []): array
    {
        return array_merge([
            'team_home' => 'Morocco',
            'team_away' => 'Portugal',
            'match_date' => '2026-06-12',
            'match_time' => '20:00',
            'stadium' => 'Grand Stade',
            'city' => 'Casablanca',
            'group_name' => 'A',
            'phase' => 'group',
            'status' => 'upcoming',
        ], $overrides);
    }

    private function hotelPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Hotel Atlas',
            'description_fr' => 'Hotel confortable proche du centre.',
            'description_en' => 'Comfortable hotel near the center.',
            'city' => 'Marrakech',
            'district' => 'Gueliz',
            'stars' => 5,
            'price_min' => 900,
            'price_max' => 1600,
            'currency' => 'MAD',
        ], $overrides);
    }

    private function restaurantPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Dar Tajine',
            'description_fr' => 'Cuisine locale traditionnelle.',
            'description_en' => 'Traditional local cuisine.',
            'city' => 'Rabat',
            'address' => 'Medina',
            'cuisine_type' => 'marocaine',
            'price_range' => 'moyen',
        ], $overrides);
    }

    private function attractionPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Medina',
            'description_fr' => 'Site culturel majeur.',
            'description_en' => 'Major cultural site.',
            'city' => 'Fes',
            'address' => 'Ancienne Medina',
            'category' => 'historique',
            'entry_price' => 0,
        ], $overrides);
    }
}
