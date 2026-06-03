<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attraction;
use App\Models\FootballMatch;
use App\Models\Hotel;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;

class FilterOptionController extends Controller
{
    public function index(): JsonResponse
    {
        $cities = collect([
            FootballMatch::query()->whereNotNull('city')->distinct()->pluck('city'),
            Hotel::query()->whereNotNull('city')->distinct()->pluck('city'),
            Restaurant::query()->whereNotNull('city')->distinct()->pluck('city'),
            Attraction::query()->whereNotNull('city')->distinct()->pluck('city'),
        ])->flatten()->map(fn ($value) => trim((string) $value))->filter()->unique()->sort()->values();

        return response()->json([
            'cities' => $cities,
            'matches' => [
                'group_names' => FootballMatch::query()
                    ->whereNotNull('group_name')
                    ->distinct()
                    ->orderBy('group_name')
                    ->pluck('group_name')
                    ->map(fn ($value) => trim((string) $value))
                    ->filter()
                    ->values(),
                'phases' => ['group', 'round_of_16', 'quarter', 'semi', 'final'],
            ],
            'hotels' => [
                'stars' => [1, 2, 3, 4, 5],
            ],
            'restaurants' => [
                'cuisine_types' => Restaurant::query()
                    ->whereNotNull('cuisine_type')
                    ->distinct()
                    ->orderBy('cuisine_type')
                    ->pluck('cuisine_type')
                    ->map(fn ($value) => trim((string) $value))
                    ->filter()
                    ->values(),
                'price_ranges' => Restaurant::query()
                    ->whereNotNull('price_range')
                    ->distinct()
                    ->orderBy('price_range')
                    ->pluck('price_range')
                    ->map(fn ($value) => trim((string) $value))
                    ->filter()
                    ->values(),
            ],
            'attractions' => [
                'categories' => Attraction::query()
                    ->whereNotNull('category')
                    ->distinct()
                    ->orderBy('category')
                    ->pluck('category')
                    ->map(fn ($value) => trim((string) $value))
                    ->filter()
                    ->values(),
            ],
        ]);
    }
}
