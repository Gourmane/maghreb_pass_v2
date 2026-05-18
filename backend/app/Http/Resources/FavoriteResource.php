<?php

namespace App\Http\Resources;

use App\Models\Attraction;
use App\Models\Hotel;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type(),
            'item' => $this->resourceForFavoriteable(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }

    private function type(): string
    {
        return match ($this->favoriteable_type) {
            Hotel::class => 'hotel',
            Restaurant::class => 'restaurant',
            Attraction::class => 'attraction',
            default => 'unknown',
        };
    }

    private function resourceForFavoriteable(): mixed
    {
        return match ($this->favoriteable_type) {
            Hotel::class => new HotelResource($this->whenLoaded('favoriteable')),
            Restaurant::class => new RestaurantResource($this->whenLoaded('favoriteable')),
            Attraction::class => new AttractionResource($this->whenLoaded('favoriteable')),
            default => $this->whenLoaded('favoriteable'),
        };
    }
}
