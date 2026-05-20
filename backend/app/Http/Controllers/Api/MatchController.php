<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttractionResource;
use App\Http\Resources\HotelResource;
use App\Http\Resources\MatchResource;
use App\Http\Resources\PackageResource;
use App\Http\Resources\RestaurantResource;
use App\Models\Attraction;
use App\Models\FootballMatch;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\TravelPackage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MatchController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $matches = FootballMatch::query()
            ->when($request->filled('city'), fn ($query) => $query->where('city', $request->input('city')))
            ->when($request->filled('group_name'), fn ($query) => $query->where('group_name', $request->input('group_name')))
            ->when($request->filled('phase'), fn ($query) => $query->where('phase', $request->input('phase')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('date'), fn ($query) => $query->whereDate('match_date', $request->input('date')))
            ->when($request->filled('match_date'), fn ($query) => $query->whereDate('match_date', $request->input('match_date')))
            ->orderBy('match_date')
            ->orderBy('match_time')
            ->paginate($request->integer('per_page', 12));

        return MatchResource::collection($matches);
    }

    public function show(int $id): MatchResource
    {
        return new MatchResource(FootballMatch::findOrFail($id));
    }

    public function nearby(Request $request, int $id)
    {
        $match = FootballMatch::findOrFail($id);
        $city = trim((string) $match->city);
        $limit = min(max($request->integer('limit', 4), 1), 12);

        if ($city === '') {
            return response()->json([
                'match_id' => $match->id,
                'city' => null,
                'hotels' => [],
                'restaurants' => [],
                'attractions' => [],
                'packages' => [],
            ]);
        }

        return response()->json([
            'match_id' => $match->id,
            'city' => $city,
            'hotels' => HotelResource::collection(
                Hotel::query()->where('city', $city)->orderByDesc('is_featured')->orderBy('name')->limit($limit)->get()
            )->resolve($request),
            'restaurants' => RestaurantResource::collection(
                Restaurant::query()->where('city', $city)->orderByDesc('is_featured')->orderBy('name')->limit($limit)->get()
            )->resolve($request),
            'attractions' => AttractionResource::collection(
                Attraction::query()->where('city', $city)->orderByDesc('is_featured')->orderBy('name')->limit($limit)->get()
            )->resolve($request),
            'packages' => PackageResource::collection(
                TravelPackage::query()->withCount('items')->where('is_active', true)->where('city', $city)->orderBy('title')->limit($limit)->get()
            )->resolve($request),
        ]);
    }
}
