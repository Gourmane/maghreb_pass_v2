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

        $data = $this->validatedItemData($request, $trip);
        $data['trip_id'] = $trip->id;
        $data['sort_order'] ??= ($trip->items()->where('day_number', $data['day_number'])->max('sort_order') ?? 0) + 1;

        $trip->items()->create($data);

        return new TripResource($trip->load('items')->loadCount('items'));
    }

    public function update(UpsertTripItemRequest $request, Trip $trip, TripItem $item): TripResource
    {
        $this->ensureOwnsTrip($request, $trip);
        $this->ensureTripItem($trip, $item);

        $item->update($this->validatedItemData($request, $trip));

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

    private function validatedItemData(UpsertTripItemRequest $request, Trip $trip): array
    {
        $data = $request->validated();

        if ($data['item_type'] === 'custom') {
            $data['item_id'] = null;

            return $data;
        }

        $item = match ($data['item_type']) {
            'hotel' => Hotel::find($data['item_id']),
            'restaurant' => Restaurant::find($data['item_id']),
            'attraction' => Attraction::find($data['item_id']),
            'match' => FootballMatch::find($data['item_id']),
            'package' => TravelPackage::find($data['item_id']),
            default => false,
        };

        if (! $item) {
            throw ValidationException::withMessages(['item_id' => 'Element introuvable.']);
        }

        if ($trip->city && $item->city && $item->city !== $trip->city) {
            throw ValidationException::withMessages(['item_id' => 'Element hors de la ville du trip.']);
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
