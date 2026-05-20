<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreHotelReservationRequest;
use App\Http\Resources\HotelReservationResource;
use App\Models\HotelReservation;
use Illuminate\Http\JsonResponse;

class HotelReservationController extends Controller
{
    public function store(StoreHotelReservationRequest $request): JsonResponse
    {
        $reservation = HotelReservation::create([
            ...$request->validated(),
            'user_id' => $request->user()?->id,
            'status' => 'pending',
        ])->load('hotel');

        return response()->json([
            'message' => 'Votre demande de reservation hotel a bien ete envoyee.',
            'data' => new HotelReservationResource($reservation),
        ], 201);
    }
}
