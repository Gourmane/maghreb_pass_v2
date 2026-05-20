<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HotelReservationResource;
use App\Http\Resources\RestaurantReservationResource;
use App\Models\HotelReservation;
use App\Models\RestaurantReservation;
use App\Notifications\ReservationStatusNotification;
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

        if (!in_array($reservation->status, ['pending', 'approved'], true) || $reservation->payment_status === 'paid') {
            return response()->json(['message' => 'Seules les demandes en attente ou approuvees non payees peuvent etre annulees.'], 422);
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

        if (!in_array($reservation->status, ['pending', 'approved'], true) || $reservation->payment_status === 'paid') {
            return response()->json(['message' => 'Seules les demandes en attente ou approuvees non payees peuvent etre annulees.'], 422);
        }

        $reservation->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Demande de reservation restaurant annulee.',
            'data' => new RestaurantReservationResource($reservation->load('restaurant')),
        ]);
    }

    public function payHotel(Request $request, HotelReservation $reservation): JsonResponse
    {
        if ($reservation->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Reservation introuvable.'], 404);
        }

        if ($reservation->status !== 'approved' || $reservation->payment_status !== 'unpaid') {
            return response()->json(['message' => 'Cette reservation ne peut pas etre payee.'], 422);
        }

        $reservation->update([
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'paid_at' => now(),
            'payment_reference' => $this->paymentReference('H', $reservation->id),
        ]);

        $reservation->load('hotel');
        ReservationStatusNotification::sendSafely($request->user(), 'paid', $reservation);

        return response()->json([
            'message' => 'Paiement simule effectue avec succes. Votre reservation est maintenant confirmee.',
            'data' => new HotelReservationResource($reservation),
        ]);
    }

    public function payRestaurant(Request $request, RestaurantReservation $reservation): JsonResponse
    {
        if ($reservation->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Reservation introuvable.'], 404);
        }

        if ($reservation->status !== 'approved' || $reservation->payment_status !== 'unpaid') {
            return response()->json(['message' => 'Cette reservation ne peut pas etre payee.'], 422);
        }

        $reservation->update([
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'paid_at' => now(),
            'payment_reference' => $this->paymentReference('R', $reservation->id),
        ]);

        $reservation->load('restaurant');
        ReservationStatusNotification::sendSafely($request->user(), 'paid', $reservation);

        return response()->json([
            'message' => 'Paiement simule effectue avec succes. Votre reservation est maintenant confirmee.',
            'data' => new RestaurantReservationResource($reservation),
        ]);
    }

    private function paymentReference(string $type, int $id): string
    {
        return 'MP-PAY-'.now()->format('Y').'-'.$type.'-'.str_pad((string) $id, 6, '0', STR_PAD_LEFT);
    }
}
