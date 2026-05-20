<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'restaurant',
            'user_id' => $this->user_id,
            'restaurant_id' => $this->restaurant_id,
            'restaurant' => new RestaurantResource($this->whenLoaded('restaurant')),
            'user' => $this->whenLoaded('user', fn () => $this->user?->only(['id', 'name', 'email'])),
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'reservation_date' => $this->reservation_date?->toDateString(),
            'reservation_time' => $this->reservation_time,
            'guests' => $this->guests,
            'message' => $this->message,
            'status' => $this->status,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
