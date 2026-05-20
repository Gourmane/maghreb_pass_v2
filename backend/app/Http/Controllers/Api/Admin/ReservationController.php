<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateReservationStatusRequest;
use App\Http\Resources\HotelReservationResource;
use App\Http\Resources\RestaurantReservationResource;
use App\Models\HotelReservation;
use App\Models\RestaurantReservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $status = $request->string('status')->trim()->value();

        $hotelReservations = HotelReservation::query()
            ->with(['hotel', 'user'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->get();

        $restaurantReservations = RestaurantReservation::query()
            ->with(['restaurant', 'user'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->get();

        return response()->json([
            'data' => [
                'hotels' => HotelReservationResource::collection($hotelReservations)->resolve(),
                'restaurants' => RestaurantReservationResource::collection($restaurantReservations)->resolve(),
            ],
        ]);
    }

    public function updateHotelStatus(UpdateReservationStatusRequest $request, HotelReservation $reservation): JsonResponse
    {
        $reservation->update($request->validated());

        return response()->json([
            'message' => 'Statut de reservation hotel mis a jour.',
            'data' => new HotelReservationResource($reservation->load(['hotel', 'user'])),
        ]);
    }

    public function updateRestaurantStatus(UpdateReservationStatusRequest $request, RestaurantReservation $reservation): JsonResponse
    {
        $reservation->update($request->validated());

        return response()->json([
            'message' => 'Statut de reservation restaurant mis a jour.',
            'data' => new RestaurantReservationResource($reservation->load(['restaurant', 'user'])),
        ]);
    }
}
