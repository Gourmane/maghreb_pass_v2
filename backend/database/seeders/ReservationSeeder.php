<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\HotelReservation;
use App\Models\Restaurant;
use App\Models\RestaurantReservation;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $tourist = User::where('role', 'tourist')->first();
        $hotel = Hotel::where('city', 'Casablanca')->first();
        $restaurant = Restaurant::where('city', 'Casablanca')->first();

        if ($hotel) {
            HotelReservation::updateOrCreate(
                ['email' => 'demo.hotel@maghrebpass.test', 'hotel_id' => $hotel->id],
                [
                    'user_id' => $tourist?->id,
                    'full_name' => 'Touriste Demo',
                    'phone' => '+212 600 000 100',
                    'check_in_date' => now()->addDays(15)->toDateString(),
                    'check_out_date' => now()->addDays(18)->toDateString(),
                    'guests' => 2,
                    'number_of_rooms' => 1,
                    'message' => 'Arrivee prevue apres le match.',
                    'status' => 'pending',
                ],
            );
        }

        if ($restaurant) {
            RestaurantReservation::updateOrCreate(
                ['email' => 'demo.restaurant@maghrebpass.test', 'restaurant_id' => $restaurant->id],
                [
                    'user_id' => $tourist?->id,
                    'full_name' => 'Touriste Demo',
                    'phone' => '+212 600 000 101',
                    'reservation_date' => now()->addDays(16)->toDateString(),
                    'reservation_time' => '20:30',
                    'guests' => 2,
                    'message' => 'Table calme si possible.',
                    'status' => 'pending',
                ],
            );
        }
    }
}
