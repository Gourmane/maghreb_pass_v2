<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\HotelReservation;
use App\Models\Restaurant;
use App\Models\RestaurantReservation;
use App\Models\User;
use App\Notifications\ReservationStatusNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReservationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_hotel_or_restaurant_reservation_requests(): void
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
            ->assertUnauthorized();

        $this->postJson('/api/restaurant-reservations', [
            'restaurant_id' => $restaurant->id,
            'full_name' => 'Guest Traveler',
            'email' => 'guest@example.test',
            'phone' => '+212 600 000 000',
            'reservation_date' => now()->addDay()->toDateString(),
            'reservation_time' => '20:30',
            'guests' => 2,
        ])
            ->assertUnauthorized();
    }

    public function test_authenticated_user_can_create_reservations_with_server_owned_status_and_payment_fields(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $hotel = Hotel::create($this->hotelPayload());
        $restaurant = Restaurant::create($this->restaurantPayload());

        Sanctum::actingAs($user);

        $this->postJson('/api/hotel-reservations', [
            'hotel_id' => $hotel->id,
            'user_id' => $otherUser->id,
            'full_name' => 'Guest Traveler',
            'email' => 'guest@example.test',
            'phone' => '+212 600 000 000',
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(3)->toDateString(),
            'guests' => 2,
            'number_of_rooms' => 1,
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'paid_at' => now()->toISOString(),
            'payment_reference' => 'BAD-REF',
        ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.payment_status', 'unpaid')
            ->assertJsonPath('data.payment_reference', null)
            ->assertJsonPath('data.user_id', $user->id);

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
            ->assertJsonPath('data.payment_status', 'unpaid')
            ->assertJsonPath('data.user_id', $user->id);

        Notification::assertSentTo($user, ReservationStatusNotification::class);
    }

    public function test_authenticated_user_can_list_and_cancel_pending_or_approved_unpaid_reservations(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::create($this->hotelPayload());
        $restaurant = Restaurant::create($this->restaurantPayload());
        $hotelReservation = HotelReservation::create($this->hotelReservationPayload($hotel, $user));
        $restaurantReservation = RestaurantReservation::create($this->restaurantReservationPayload($restaurant, $user, ['status' => 'approved']));

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
        $confirmed = HotelReservation::create($this->hotelReservationPayload($hotel, $user, ['status' => 'confirmed', 'payment_status' => 'paid']));
        $other = HotelReservation::create($this->hotelReservationPayload($hotel, $otherUser));

        Sanctum::actingAs($user);

        $this->putJson("/api/my-hotel-reservations/{$confirmed->id}/cancel")
            ->assertStatus(422);

        $this->putJson("/api/my-hotel-reservations/{$other->id}/cancel")
            ->assertNotFound();
    }

    public function test_admin_can_list_approve_and_reject_reservation_statuses(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $tourist = User::factory()->create();
        $hotel = Hotel::create($this->hotelPayload());
        $restaurant = Restaurant::create($this->restaurantPayload());
        $hotelReservation = HotelReservation::create($this->hotelReservationPayload($hotel, $tourist));
        $restaurantReservation = RestaurantReservation::create($this->restaurantReservationPayload($restaurant, $tourist));

        Sanctum::actingAs($admin);

        $this->getJson('/api/admin/reservations')
            ->assertOk()
            ->assertJsonCount(1, 'data.hotels')
            ->assertJsonCount(1, 'data.restaurants');

        $this->putJson("/api/admin/hotel-reservations/{$hotelReservation->id}/status", ['status' => 'approved'])
            ->assertOk()
            ->assertJsonPath('data.status', 'approved')
            ->assertJsonPath('data.payment_status', 'unpaid');

        $this->putJson("/api/admin/restaurant-reservations/{$restaurantReservation->id}/status", ['status' => 'rejected'])
            ->assertOk()
            ->assertJsonPath('data.status', 'rejected');

        $this->putJson("/api/admin/hotel-reservations/{$hotelReservation->id}/status", ['status' => 'confirmed'])
            ->assertStatus(422);

        $this->getJson('/api/admin/stats')
            ->assertOk()
            ->assertJsonPath('approved_reservations', 1)
            ->assertJsonPath('rejected_reservations', 1)
            ->assertJsonPath('unpaid_reservations', 2);

        Notification::assertSentTo($tourist, ReservationStatusNotification::class);
    }

    public function test_tourist_can_complete_simulated_payment_for_approved_unpaid_reservation(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $hotel = Hotel::create($this->hotelPayload());
        $reservation = HotelReservation::create($this->hotelReservationPayload($hotel, $user, ['status' => 'approved']));

        Sanctum::actingAs($user);

        $this->postJson("/api/my-hotel-reservations/{$reservation->id}/pay")
            ->assertOk()
            ->assertJsonPath('data.status', 'confirmed')
            ->assertJsonPath('data.payment_status', 'paid')
            ->assertJsonPath('data.payment_reference', 'MP-PAY-'.now()->format('Y').'-H-'.str_pad((string) $reservation->id, 6, '0', STR_PAD_LEFT))
            ->assertJsonPath('message', 'Paiement simule effectue avec succes. Votre reservation est maintenant confirmee.');

        $reservation->refresh();

        $this->assertNotNull($reservation->paid_at);
        Notification::assertSentTo($user, ReservationStatusNotification::class);
    }

    public function test_tourist_cannot_pay_other_users_or_invalid_state_reservation(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $hotel = Hotel::create($this->hotelPayload());
        $otherReservation = HotelReservation::create($this->hotelReservationPayload($hotel, $otherUser, ['status' => 'approved']));
        $pendingReservation = HotelReservation::create($this->hotelReservationPayload($hotel, $user));

        Sanctum::actingAs($user);

        $this->postJson("/api/my-hotel-reservations/{$otherReservation->id}/pay")
            ->assertNotFound();

        $this->postJson("/api/my-hotel-reservations/{$pendingReservation->id}/pay")
            ->assertStatus(422);
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
            'payment_status' => 'unpaid',
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
            'payment_status' => 'unpaid',
        ], $overrides);
    }
}
