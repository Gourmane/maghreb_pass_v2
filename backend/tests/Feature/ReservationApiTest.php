<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\HotelReservation;
use App\Models\Restaurant;
use App\Models\RestaurantReservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReservationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_create_hotel_and_restaurant_reservation_requests(): void
    {
        $hotel = Hotel::create($this->hotelPayload());
        $restaurant = Restaurant::create($this->restaurantPayload());

        $this->postJson('/api/hotel-reservations', [
            'hotel_id' => $hotel->id,
            'full_name' => 'Guest Traveler',
            'email' => 'guest@example.test',
            'phone' => '+212 600 000 000',
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(3)->toDateString(),
            'guests' => 2,
            'number_of_rooms' => 1,
            'message' => 'Late check-in.',
        ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.user_id', null);

        $this->postJson('/api/restaurant-reservations', [
            'restaurant_id' => $restaurant->id,
            'full_name' => 'Guest Traveler',
            'email' => 'guest@example.test',
            'phone' => '+212 600 000 000',
            'reservation_date' => now()->addDay()->toDateString(),
            'reservation_time' => '20:30',
            'guests' => 2,
        ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.user_id', null);
    }

    public function test_authenticated_user_can_list_and_cancel_pending_reservations(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::create($this->hotelPayload());
        $restaurant = Restaurant::create($this->restaurantPayload());
        $hotelReservation = HotelReservation::create($this->hotelReservationPayload($hotel, $user));
        $restaurantReservation = RestaurantReservation::create($this->restaurantReservationPayload($restaurant, $user));

        Sanctum::actingAs($user);

        $this->getJson('/api/my-reservations')
            ->assertOk()
            ->assertJsonCount(1, 'data.hotels')
            ->assertJsonCount(1, 'data.restaurants');

        $this->putJson("/api/my-hotel-reservations/{$hotelReservation->id}/cancel")
            ->assertOk()
            ->assertJsonPath('data.status', 'cancelled');

        $this->putJson("/api/my-restaurant-reservations/{$restaurantReservation->id}/cancel")
            ->assertOk()
            ->assertJsonPath('data.status', 'cancelled');
    }

    public function test_user_cannot_cancel_confirmed_or_other_users_reservation(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $hotel = Hotel::create($this->hotelPayload());
        $confirmed = HotelReservation::create($this->hotelReservationPayload($hotel, $user, ['status' => 'confirmed']));
        $other = HotelReservation::create($this->hotelReservationPayload($hotel, $otherUser));

        Sanctum::actingAs($user);

        $this->putJson("/api/my-hotel-reservations/{$confirmed->id}/cancel")
            ->assertStatus(422);

        $this->putJson("/api/my-hotel-reservations/{$other->id}/cancel")
            ->assertNotFound();
    }

    public function test_admin_can_list_and_update_reservation_statuses(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $hotel = Hotel::create($this->hotelPayload());
        $restaurant = Restaurant::create($this->restaurantPayload());
        $hotelReservation = HotelReservation::create($this->hotelReservationPayload($hotel));
        $restaurantReservation = RestaurantReservation::create($this->restaurantReservationPayload($restaurant));

        Sanctum::actingAs($admin);

        $this->getJson('/api/admin/reservations')
            ->assertOk()
            ->assertJsonCount(1, 'data.hotels')
            ->assertJsonCount(1, 'data.restaurants');

        $this->putJson("/api/admin/hotel-reservations/{$hotelReservation->id}/status", ['status' => 'confirmed'])
            ->assertOk()
            ->assertJsonPath('data.status', 'confirmed');

        $this->putJson("/api/admin/restaurant-reservations/{$restaurantReservation->id}/status", ['status' => 'rejected'])
            ->assertOk()
            ->assertJsonPath('data.status', 'rejected');

        $this->getJson('/api/admin/stats')
            ->assertOk()
            ->assertJsonPath('reservations_confirmed', 1);
    }

    private function hotelPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Hotel Atlas',
            'description_fr' => 'Hotel confortable.',
            'description_en' => 'Comfortable hotel.',
            'city' => 'Casablanca',
            'stars' => 4,
            'price_min' => 700,
            'price_max' => 1200,
            'currency' => 'MAD',
        ], $overrides);
    }

    private function restaurantPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Dar Tajine',
            'description_fr' => 'Cuisine locale.',
            'description_en' => 'Local cuisine.',
            'city' => 'Casablanca',
            'cuisine_type' => 'Marocaine',
            'price_range' => 'moyen',
        ], $overrides);
    }

    private function hotelReservationPayload(Hotel $hotel, ?User $user = null, array $overrides = []): array
    {
        return array_merge([
            'user_id' => $user?->id,
            'hotel_id' => $hotel->id,
            'full_name' => 'Traveler',
            'email' => 'traveler@example.test',
            'phone' => '+212 600 000 000',
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'guests' => 2,
            'number_of_rooms' => 1,
            'status' => 'pending',
        ], $overrides);
    }

    private function restaurantReservationPayload(Restaurant $restaurant, ?User $user = null, array $overrides = []): array
    {
        return array_merge([
            'user_id' => $user?->id,
            'restaurant_id' => $restaurant->id,
            'full_name' => 'Traveler',
            'email' => 'traveler@example.test',
            'phone' => '+212 600 000 000',
            'reservation_date' => now()->addDay()->toDateString(),
            'reservation_time' => '20:30',
            'guests' => 2,
            'status' => 'pending',
        ], $overrides);
    }
}
