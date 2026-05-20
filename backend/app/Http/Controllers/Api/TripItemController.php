<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpsertTripItemRequest;
use App\Http\Resources\TripResource;
use App\Models\Attraction;
use App\Models\FootballMatch;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\TravelPackage;
use App\Models\Trip;
use App\Models\TripItem;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TripItemController extends Controller
{
    public function store(UpsertTripItemRequest $request, Trip $trip): TripResource
    {
        $this->ensureOwnsTrip($request, $trip);
        $this->ensureLimit($trip);

        $data = $this->validatedItemData($request);
        $data['trip_id'] = $trip->id;
        $data['sort_order'] ??= ($trip->items()->where('day_number', $data['day_number'])->max('sort_order') ?? 0) + 1;

        $trip->items()->create($data);

        return new TripResource($trip->load('items')->loadCount('items'));
    }

    public function update(UpsertTripItemRequest $request, Trip $trip, TripItem $item): TripResource
    {
        $this->ensureOwnsTrip($request, $trip);
        $this->ensureTripItem($trip, $item);

        $item->update($this->validatedItemData($request));

        return new TripResource($trip->load('items')->loadCount('items'));
    }

    public function destroy(Request $request, Trip $trip, TripItem $item): TripResource
    {
        $this->ensureOwnsTrip($request, $trip);
        $this->ensureTripItem($trip, $item);
        $item->delete();
        $this->normalizeSortOrder($trip);

        return new TripResource($trip->load('items')->loadCount('items'));
    }

    private function validatedItemData(UpsertTripItemRequest $request): array
    {
        $data = $request->validated();

        if ($data['item_type'] === 'custom') {
            $data['item_id'] = null;

            return $data;
        }

        $exists = match ($data['item_type']) {
            'hotel' => Hotel::whereKey($data['item_id'])->exists(),
            'restaurant' => Restaurant::whereKey($data['item_id'])->exists(),
            'attraction' => Attraction::whereKey($data['item_id'])->exists(),
            'match' => FootballMatch::whereKey($data['item_id'])->exists(),
            'package' => TravelPackage::whereKey($data['item_id'])->exists(),
            default => false,
        };

        if (! $exists) {
            throw ValidationException::withMessages(['item_id' => 'Element introuvable.']);
        }

        $data['custom_title'] = null;
        $data['custom_description'] = null;

        return $data;
    }

    private function ensureLimit(Trip $trip): void
    {
        if ($trip->items()->count() >= 30) {
            throw ValidationException::withMessages(['items' => 'Un trip ne peut pas depasser 30 elements.']);
        }
    }

    private function ensureOwnsTrip(Request $request, Trip $trip): void
    {
        abort_unless($trip->user_id === $request->user()->id, 404);
    }

    private function ensureTripItem(Trip $trip, TripItem $item): void
    {
        abort_unless($item->trip_id === $trip->id, 404);
    }

    private function normalizeSortOrder(Trip $trip): void
    {
        $trip->items()->get()->groupBy('day_number')->each(function ($items) {
            $items->values()->each(fn (TripItem $item, int $index) => $item->update(['sort_order' => $index + 1]));
        });
    }
}
