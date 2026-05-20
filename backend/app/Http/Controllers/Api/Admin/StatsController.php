<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attraction;
use App\Models\Favorite;
use App\Models\FootballMatch;
use App\Models\Hotel;
use App\Models\HotelReservation;
use App\Models\Restaurant;
use App\Models\RestaurantReservation;
use App\Models\Trip;
use App\Models\TravelPackage;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $statusCount = fn (string $status): int => HotelReservation::where('status', $status)->count()
            + RestaurantReservation::where('status', $status)->count();
        $paymentCount = fn (string $status): int => HotelReservation::where('payment_status', $status)->count()
            + RestaurantReservation::where('payment_status', $status)->count();

        return response()->json([
            'matches' => FootballMatch::count(),
            'hotels' => Hotel::count(),
            'restaurants' => Restaurant::count(),
            'attractions' => Attraction::count(),
            'packages' => TravelPackage::count(),
            'favorites' => Favorite::count(),
            'hotel_reservations_pending' => HotelReservation::where('status', 'pending')->count(),
            'restaurant_reservations_pending' => RestaurantReservation::where('status', 'pending')->count(),
            'reservations_pending' => $statusCount('pending'),
            'reservations_confirmed' => $statusCount('confirmed'),
            'users' => User::count(),
            'tourists' => User::where('role', 'tourist')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'total_users' => User::count(),
            'total_tourists' => User::where('role', 'tourist')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_trips' => Trip::count(),
            'pending_reservations' => $statusCount('pending'),
            'approved_reservations' => $statusCount('approved'),
            'confirmed_reservations' => $statusCount('confirmed'),
            'rejected_reservations' => $statusCount('rejected'),
            'cancelled_reservations' => $statusCount('cancelled'),
            'paid_reservations' => $paymentCount('paid'),
            'unpaid_reservations' => $paymentCount('unpaid'),
            'active_packages' => TravelPackage::where('is_active', true)->count(),
        ]);
    }
}
