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
use App\Models\TravelPackage;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'matches' => FootballMatch::count(),
            'hotels' => Hotel::count(),
            'restaurants' => Restaurant::count(),
            'attractions' => Attraction::count(),
            'packages' => TravelPackage::count(),
            'favorites' => Favorite::count(),
            'hotel_reservations_pending' => HotelReservation::where('status', 'pending')->count(),
            'restaurant_reservations_pending' => RestaurantReservation::where('status', 'pending')->count(),
            'reservations_pending' => HotelReservation::where('status', 'pending')->count() + RestaurantReservation::where('status', 'pending')->count(),
            'reservations_confirmed' => HotelReservation::where('status', 'confirmed')->count() + RestaurantReservation::where('status', 'confirmed')->count(),
            'users' => User::count(),
            'tourists' => User::where('role', 'tourist')->count(),
            'admins' => User::where('role', 'admin')->count(),
        ]);
    }
}
