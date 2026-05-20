<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'hotel',
            'user_id' => $this->user_id,
            'hotel_id' => $this->hotel_id,
            'hotel' => new HotelResource($this->whenLoaded('hotel')),
            'user' => $this->whenLoaded('user', fn () => $this->user?->only(['id', 'name', 'email'])),
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'check_in_date' => $this->check_in_date?->toDateString(),
            'check_out_date' => $this->check_out_date?->toDateString(),
            'guests' => $this->guests,
            'number_of_rooms' => $this->number_of_rooms,
            'message' => $this->message,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'paid_at' => $this->paid_at?->toISOString(),
            'payment_reference' => $this->payment_reference,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
