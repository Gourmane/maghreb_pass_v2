<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description_fr' => $this->description_fr,
            'description_en' => $this->description_en,
            'city' => $this->city,
            'address' => $this->address,
            'cuisine_type' => $this->cuisine_type,
            'price_range' => $this->price_range,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'map_url' => $this->map_url,
            'is_featured' => $this->is_featured,
            'rating' => $this->rating,
            'opening_hours' => $this->opening_hours,
            'image_url' => $this->image_url,
            'photos' => $this->photos ?? [],
        ];
    }
}
