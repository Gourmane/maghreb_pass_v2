<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpsertTripRequest;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $trips = $request->user()
            ->trips()
            ->with('items')
            ->withCount('items')
            ->orderBy('start_date')
            ->latest('id')
            ->get();

        return response()->json([
            'data' => TripResource::collection($trips)->resolve(),
        ]);
    }

    public function store(UpsertTripRequest $request): JsonResponse
    {
        $trip = $request->user()->trips()->create($request->validated());

        return response()->json([
            'message' => 'Trip cree.',
            'data' => new TripResource($trip->load('items')->loadCount('items')),
        ], 201);
    }

    public function show(Request $request, Trip $trip): TripResource
    {
        $this->ensureOwnsTrip($request, $trip);

        return new TripResource($trip->load('items')->loadCount('items'));
    }

    public function update(UpsertTripRequest $request, Trip $trip): TripResource
    {
        $this->ensureOwnsTrip($request, $trip);
        $trip->update($request->validated());

        return new TripResource($trip->load('items')->loadCount('items'));
    }

    public function destroy(Request $request, Trip $trip): JsonResponse
    {
        $this->ensureOwnsTrip($request, $trip);
        $trip->delete();

        return response()->json([
            'message' => 'Trip supprime.',
        ]);
    }

    private function ensureOwnsTrip(Request $request, Trip $trip): void
    {
        abort_unless($trip->user_id === $request->user()->id, 404);
    }
}
