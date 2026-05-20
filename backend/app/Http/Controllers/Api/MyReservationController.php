<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HotelReservationResource;
use App\Http\Resources\RestaurantReservationResource;
use App\Models\HotelReservation;
use App\Models\RestaurantReservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MyReservationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $hotelReservations = $request->user()
            ->hotelReservations()
            ->with('hotel')
            ->latest()
            ->get();

        $restaurantReservations = $request->user()
            ->restaurantReservations()
            ->with('restaurant')
            ->latest()
            ->get();

        return response()->json([
            'data' => [
                'hotels' => HotelReservationResource::collection($hotelReservations)->resolve(),
                'restaurants' => RestaurantReservationResource::collection($restaurantReservations)->resolve(),
            ],
        ]);
    }

    public function cancelHotel(Request $request, HotelReservation $reservation): JsonResponse
    {
        if ($reservation->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Reservation introuvable.'], 404);
        }

        if ($reservation->status !== 'pending') {
            return response()->json(['message' => 'Seules les demandes en attente peuvent etre annulees.'], 422);
        }

        $reservation->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Demande de reservation hotel annulee.',
            'data' => new HotelReservationResource($reservation->load('hotel')),
        ]);
    }

    public function cancelRestaurant(Request $request, RestaurantReservation $reservation): JsonResponse
    {
        if ($reservation->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Reservation introuvable.'], 404);
        }

        if ($reservation->status !== 'pending') {
            return response()->json(['message' => 'Seules les demandes en attente peuvent etre annulees.'], 422);
        }

        $reservation->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Demande de reservation restaurant annulee.',
            'data' => new RestaurantReservationResource($reservation->load('restaurant')),
        ]);
    }
}
