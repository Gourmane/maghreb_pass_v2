<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpsertPackageItemRequest;
use App\Http\Resources\PackageResource;
use App\Models\Attraction;
use App\Models\FootballMatch;
use App\Models\Hotel;
use App\Models\PackageItem;
use App\Models\Restaurant;
use App\Models\TravelPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class PackageItemController extends Controller
{
    public function store(UpsertPackageItemRequest $request, TravelPackage $package): PackageResource
    {
        $this->ensureLimit($package);
        $data = $this->validatedItemData($request);
        $data['package_id'] = $package->id;
        $data['sort_order'] ??= ($package->items()->where('day_number', $data['day_number'])->max('sort_order') ?? 0) + 1;

        $package->items()->create($data);

        return new PackageResource($package->load('items')->loadCount('items'));
    }

    public function update(UpsertPackageItemRequest $request, TravelPackage $package, PackageItem $item): PackageResource
    {
        $this->ensurePackageItem($package, $item);
        $item->update($this->validatedItemData($request));

        return new PackageResource($package->load('items')->loadCount('items'));
    }

    public function destroy(TravelPackage $package, PackageItem $item): PackageResource
    {
        $this->ensurePackageItem($package, $item);
        $item->delete();
        $this->normalizeSortOrder($package);

        return new PackageResource($package->load('items')->loadCount('items'));
    }

    public function move(TravelPackage $package, PackageItem $item, string $direction): PackageResource|JsonResponse
    {
        $this->ensurePackageItem($package, $item);

        if (! in_array($direction, ['up', 'down'], true)) {
            return response()->json(['message' => 'Direction invalide.'], 422);
        }

        $operator = $direction === 'up' ? '<' : '>';
        $order = $direction === 'up' ? 'desc' : 'asc';
        $swap = $package->items()
            ->where('day_number', $item->day_number)
            ->where('sort_order', $operator, $item->sort_order)
            ->orderBy('sort_order', $order)
            ->first();

        if ($swap) {
            [$itemOrder, $swapOrder] = [$item->sort_order, $swap->sort_order];
            $item->update(['sort_order' => $swapOrder]);
            $swap->update(['sort_order' => $itemOrder]);
        }

        return new PackageResource($package->load('items')->loadCount('items'));
    }

    private function validatedItemData(UpsertPackageItemRequest $request): array
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
            default => false,
        };

        if (! $exists) {
            throw ValidationException::withMessages(['item_id' => 'Element introuvable.']);
        }

        $data['custom_title'] = null;
        $data['custom_description'] = null;

        return $data;
    }

    private function ensureLimit(TravelPackage $package): void
    {
        if ($package->items()->count() >= 30) {
            throw ValidationException::withMessages(['items' => 'Un package ne peut pas depasser 30 elements.']);
        }
    }

    private function ensurePackageItem(TravelPackage $package, PackageItem $item): void
    {
        abort_unless($item->package_id === $package->id, 404);
    }

    private function normalizeSortOrder(TravelPackage $package): void
    {
        $package->items()->get()->groupBy('day_number')->each(function ($items) {
            $items->values()->each(fn (PackageItem $item, int $index) => $item->update(['sort_order' => $index + 1]));
        });
    }
}
