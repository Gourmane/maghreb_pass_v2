<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attraction;
use App\Models\FootballMatch;
use App\Models\Hotel;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MapItemController extends Controller
{
    private const DEFAULT_CITY = 'Casablanca';

    public function __invoke(Request $request): JsonResponse
    {
        $city = $request->string('city')->trim()->value() ?: self::DEFAULT_CITY;
        $type = $request->string('type')->trim()->lower()->value() ?: 'all';

        if (! in_array($type, ['all', 'hotel', 'restaurant', 'attraction', 'match'], true)) {
            return response()->json([
                'message' => 'Type de carte invalide.',
            ], 422);
        }

        return response()->json([
            'city' => $city,
            'type' => $type,
            'hotels' => in_array($type, ['all', 'hotel'], true) ? $this->hotels($city) : [],
            'restaurants' => in_array($type, ['all', 'restaurant'], true) ? $this->restaurants($city) : [],
            'attractions' => in_array($type, ['all', 'attraction'], true) ? $this->attractions($city) : [],
            'matches' => in_array($type, ['all', 'match'], true) ? $this->matches($city) : [],
        ]);
    }

    private function hotels(string $city): array
    {
        return Hotel::query()
            ->where('city', $city)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->get()
            ->map(fn (Hotel $hotel) => [
                'id' => $hotel->id,
                'type' => 'hotel',
                'name' => $hotel->name,
                'city' => $hotel->city,
                'latitude' => $hotel->latitude,
                'longitude' => $hotel->longitude,
                'image' => $hotel->image_url ?: data_get($hotel->photos, 0),
                'rating' => $hotel->rating,
                'detail_url' => "/hotels/{$hotel->id}",
            ])
            ->all();
    }

    private function restaurants(string $city): array
    {
        return Restaurant::query()
            ->where('city', $city)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->get()
            ->map(fn (Restaurant $restaurant) => [
                'id' => $restaurant->id,
                'type' => 'restaurant',
                'name' => $restaurant->name,
                'city' => $restaurant->city,
                'latitude' => $restaurant->latitude,
                'longitude' => $restaurant->longitude,
                'image' => $restaurant->image_url ?: data_get($restaurant->photos, 0),
                'rating' => $restaurant->rating,
                'detail_url' => "/restaurants/{$restaurant->id}",
            ])
            ->all();
    }

    private function attractions(string $city): array
    {
        return Attraction::query()
            ->where('city', $city)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->get()
            ->map(fn (Attraction $attraction) => [
                'id' => $attraction->id,
                'type' => 'attraction',
                'name' => $attraction->name,
                'city' => $attraction->city,
                'latitude' => $attraction->latitude,
                'longitude' => $attraction->longitude,
                'image' => $attraction->image_url ?: data_get($attraction->photos, 0),
                'rating' => $attraction->rating,
                'detail_url' => "/attractions/{$attraction->id}",
            ])
            ->all();
    }

    private function matches(string $city): array
    {
        return FootballMatch::query()
            ->where('city', $city)
            ->whereNotNull('stadium_latitude')
            ->whereNotNull('stadium_longitude')
            ->orderBy('match_date')
            ->orderBy('match_time')
            ->get()
            ->map(fn (FootballMatch $match) => [
                'id' => $match->id,
                'type' => 'match',
                'name' => "{$match->team_home} vs {$match->team_away}",
                'city' => $match->city,
                'latitude' => $match->stadium_latitude,
                'longitude' => $match->stadium_longitude,
                'image' => $match->team_home_flag_url,
                'rating' => null,
                'detail_url' => "/matches/{$match->id}",
            ])
            ->all();
    }
}
