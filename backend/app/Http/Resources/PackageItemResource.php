<?php

namespace App\Http\Resources;

use App\Models\Attraction;
use App\Models\FootballMatch;
use App\Models\Hotel;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'package_id' => $this->package_id,
            'item_type' => $this->item_type,
            'item_id' => $this->item_id,
            'custom_title' => $this->custom_title,
            'custom_description' => $this->custom_description,
            'day_number' => $this->day_number,
            'sort_order' => $this->sort_order,
            'item' => $this->summary(),
        ];
    }

    private function summary(): ?array
    {
        if ($this->item_type === 'custom') {
            return [
                'title' => $this->custom_title,
                'description' => $this->custom_description,
            ];
        }

        $model = match ($this->item_type) {
            'hotel' => Hotel::find($this->item_id),
            'restaurant' => Restaurant::find($this->item_id),
            'attraction' => Attraction::find($this->item_id),
            'match' => FootballMatch::find($this->item_id),
            default => null,
        };

        if (! $model) {
            return null;
        }

        if ($model instanceof FootballMatch) {
            return [
                'title' => "{$model->team_home} vs {$model->team_away}",
                'description' => $model->stadium,
                'city' => $model->city,
            ];
        }

        return [
            'title' => $model->name,
            'description_fr' => $model->description_fr,
            'description_en' => $model->description_en,
            'city' => $model->city,
            'image_url' => $model->image_url,
        ];
    }
}
