<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreFavoriteRequest;
use App\Http\Resources\FavoriteResource;
use App\Models\Attraction;
use App\Models\Favorite;
use App\Models\Hotel;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    private const TYPES = [
        'hotel' => Hotel::class,
        'restaurant' => Restaurant::class,
        'attraction' => Attraction::class,
    ];

    public function index(Request $request): JsonResponse
    {
        $favorites = $request->user()
            ->favorites()
            ->with('favoriteable')
            ->latest()
            ->get();

        return response()->json([
            'data' => [
                'hotels' => FavoriteResource::collection($favorites->where('favoriteable_type', Hotel::class))->resolve(),
                'restaurants' => FavoriteResource::collection($favorites->where('favoriteable_type', Restaurant::class))->resolve(),
                'attractions' => FavoriteResource::collection($favorites->where('favoriteable_type', Attraction::class))->resolve(),
            ],
        ]);
    }

    public function store(StoreFavoriteRequest $request): JsonResponse
    {
        $favoriteableType = self::TYPES[$request->input('type')];
        $favoriteable = $favoriteableType::find($request->integer('id'));

        if (! $favoriteable) {
            return response()->json([
                'message' => 'Element introuvable.',
            ], 404);
        }

        $favorite = Favorite::firstOrCreate([
            'user_id' => $request->user()->id,
            'favoriteable_id' => $favoriteable->id,
            'favoriteable_type' => $favoriteableType,
        ]);

        $favorite->load('favoriteable');

        return response()->json([
            'message' => $favorite->wasRecentlyCreated ? 'Favori ajoute.' : 'Favori deja existant.',
            'favorite' => new FavoriteResource($favorite),
        ], $favorite->wasRecentlyCreated ? 201 : 200);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $favorite = $request->user()
            ->favorites()
            ->whereKey($id)
            ->first();

        if (! $favorite) {
            return response()->json([
                'message' => 'Favori introuvable.',
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'message' => 'Favori retire.',
        ]);
    }
}
