<?php

namespace Tests\Feature;

use App\Models\Attraction;
use App\Models\FootballMatch;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\TravelPackage;
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

    public function test_map_items_returns_geolocated_items_for_selected_city(): void
    {
        Hotel::create($this->hotelPayload([
            'city' => 'Casablanca',
            'latitude' => 33.5983,
            'longitude' => -7.6642,
            'image_url' => 'https://example.test/hotel.jpg',
            'rating' => 4.8,
        ]));
        Hotel::create($this->hotelPayload([
            'name' => 'Hotel Without GPS',
            'city' => 'Casablanca',
        ]));
        Restaurant::create($this->restaurantPayload([
            'city' => 'Casablanca',
            'latitude' => 33.6034,
            'longitude' => -7.6196,
        ]));
        Attraction::create($this->attractionPayload([
            'city' => 'Casablanca',
            'latitude' => 33.6084,
            'longitude' => -7.6326,
        ]));
        FootballMatch::create($this->matchPayload([
            'city' => 'Casablanca',
            'stadium_latitude' => 33.5248,
            'stadium_longitude' => -7.6501,
        ]));

        $this->getJson('/api/map-items?city=Casablanca&type=all')
            ->assertOk()
            ->assertJsonPath('city', 'Casablanca')
            ->assertJsonCount(1, 'hotels')
            ->assertJsonCount(1, 'restaurants')
            ->assertJsonCount(1, 'attractions')
            ->assertJsonCount(1, 'matches')
            ->assertJsonPath('hotels.0.type', 'hotel')
            ->assertJsonPath('hotels.0.detail_url', '/hotels/1');
    }

    public function test_map_items_can_filter_by_type(): void
    {
        Hotel::create($this->hotelPayload([
            'city' => 'Casablanca',
            'latitude' => 33.5983,
            'longitude' => -7.6642,
        ]));
        Restaurant::create($this->restaurantPayload([
            'city' => 'Casablanca',
            'latitude' => 33.6034,
            'longitude' => -7.6196,
        ]));

        $this->getJson('/api/map-items?city=Casablanca&type=restaurant')
            ->assertOk()
            ->assertJsonCount(0, 'hotels')
            ->assertJsonCount(1, 'restaurants')
            ->assertJsonPath('restaurants.0.type', 'restaurant');
    }

    public function test_match_nearby_returns_same_city_catalog_sections(): void
    {
        $match = FootballMatch::create($this->matchPayload(['city' => 'Casablanca']));
        Hotel::create($this->hotelPayload(['city' => 'Casablanca']));
        Hotel::create($this->hotelPayload(['name' => 'Rabat Hotel', 'city' => 'Rabat']));
        Restaurant::create($this->restaurantPayload(['city' => 'Casablanca']));
        Attraction::create($this->attractionPayload(['city' => 'Casablanca']));
        TravelPackage::create($this->packagePayload(['city' => 'Casablanca']));
        TravelPackage::create($this->packagePayload(['title' => 'Hidden Plan', 'city' => 'Casablanca', 'is_active' => false]));

        $this->getJson("/api/matches/{$match->id}/nearby")
            ->assertOk()
            ->assertJsonPath('match_id', $match->id)
            ->assertJsonPath('city', 'Casablanca')
            ->assertJsonCount(1, 'hotels')
            ->assertJsonCount(1, 'restaurants')
            ->assertJsonCount(1, 'attractions')
            ->assertJsonCount(1, 'packages')
            ->assertJsonPath('hotels.0.city', 'Casablanca')
            ->assertJsonPath('packages.0.is_active', true);
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

    private function packagePayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Casablanca Weekend',
            'description_fr' => 'Programme touristique.',
            'description_en' => 'Travel program.',
            'city' => 'Casablanca',
            'price_min' => 1200,
            'price_max' => 2400,
            'currency' => 'MAD',
            'is_active' => true,
        ], $overrides);
    }
}
