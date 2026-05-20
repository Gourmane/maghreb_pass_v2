<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description_fr' => $this->description_fr,
            'description_en' => $this->description_en,
            'city' => $this->city,
            'district' => $this->district,
            'stars' => $this->stars,
            'price_min' => $this->price_min,
            'price_max' => $this->price_max,
            'currency' => $this->currency,
            'website_url' => $this->website_url,
            'phone' => $this->phone,
            'email' => $this->email,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'map_url' => $this->map_url,
            'is_featured' => $this->is_featured,
            'rating' => $this->rating,
            'amenities' => $this->amenities ?? [],
            'image_url' => $this->image_url,
            'photos' => $this->photos ?? [],
        ];
    }
}
