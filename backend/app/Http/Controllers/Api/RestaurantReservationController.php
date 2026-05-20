<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreRestaurantReservationRequest;
use App\Http\Resources\RestaurantReservationResource;
use App\Models\RestaurantReservation;
use App\Notifications\ReservationStatusNotification;
use Illuminate\Http\JsonResponse;

class RestaurantReservationController extends Controller
{
    public function store(StoreRestaurantReservationRequest $request): JsonResponse
    {
        $reservation = RestaurantReservation::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'paid_at' => null,
            'payment_reference' => null,
        ])->load('restaurant');

        ReservationStatusNotification::sendSafely($request->user(), 'created', $reservation);

        return response()->json([
            'message' => 'Votre demande de reservation restaurant a bien ete envoyee.',
            'data' => new RestaurantReservationResource($reservation),
        ], 201);
    }
}
