<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title_fr,
            'name' => $this->title_fr,
            'title_fr' => $this->title_fr,
            'title_en' => $this->title_en,
            'description_fr' => $this->description_fr,
            'description_en' => $this->description_en,
            'city' => $this->city,
            'price_min' => $this->price_min,
            'price_max' => $this->price_max,
            'currency' => $this->currency,
            'image_url' => $this->image_url,
            'is_active' => $this->is_active,
            'items_count' => $this->whenCounted('items'),
            'items' => PackageItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
