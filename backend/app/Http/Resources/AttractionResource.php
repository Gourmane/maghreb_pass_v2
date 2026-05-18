<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttractionResource extends JsonResource
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
            'category' => $this->category,
            'entry_price' => $this->entry_price,
            'opening_hours' => $this->opening_hours,
            'photos' => $this->photos ?? [],
        ];
    }
}
