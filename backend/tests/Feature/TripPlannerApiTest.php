<?php

namespace Tests\Feature;

use App\Models\Favorite;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\Trip;
use App\Models\TripItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TripPlannerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_trips(): void
    {
        $this->getJson('/api/trips')->assertUnauthorized();
        $this->postJson('/api/trips', ['title' => 'Casablanca'])->assertUnauthorized();
    }

    public function test_user_can_create_update_and_delete_trip_with_items(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::create($this->hotelPayload());

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/trips', [
            'title' => 'Casablanca match week',
            'city' => 'Casablanca',
            'start_date' => '2030-06-14',
            'end_date' => '2030-06-17',
        ])->assertCreated()
            ->assertJsonPath('data.title', 'Casablanca match week');

        $tripId = $response->json('data.id');

        $itemResponse = $this->postJson("/api/trips/{$tripId}/items", [
            'item_type' => 'hotel',
            'item_id' => $hotel->id,
            'day_number' => 1,
            'start_time' => '15:30',
            'notes' => 'Check-in before dinner.',
        ])->assertOk()
            ->assertJsonPath('data.items.0.item.title', 'Hotel Atlas')
            ->assertJsonPath('data.items.0.start_time', '15:30');

        $itemId = $itemResponse->json('data.items.0.id');

        $this->putJson("/api/trips/{$tripId}/items/{$itemId}", [
            'item_type' => 'custom',
            'custom_title' => 'Walk through the medina',
            'custom_description' => 'Flexible evening slot.',
            'day_number' => 2,
        ])->assertOk()
            ->assertJsonPath('data.items.0.item.title', 'Walk through the medina')
            ->assertJsonPath('data.items.0.day_number', 2);

        $this->deleteJson("/api/trips/{$tripId}/items/{$itemId}")
            ->assertOk()
            ->assertJsonCount(0, 'data.items');

        $this->deleteJson("/api/trips/{$tripId}")->assertOk();

        $this->assertDatabaseMissing('trips', ['id' => $tripId]);
    }

    public function test_trip_items_cascade_delete_and_are_limited_to_thirty(): void
    {
        $user = User::factory()->create();
        $trip = Trip::create([
            'user_id' => $user->id,
            'title' => 'Full itinerary',
        ]);

        for ($index = 1; $index <= 30; $index++) {
            TripItem::create([
                'trip_id' => $trip->id,
                'item_type' => 'custom',
                'custom_title' => "Stop {$index}",
                'day_number' => 1,
                'sort_order' => $index,
            ]);
        }

        Sanctum::actingAs($user);

        $this->postJson("/api/trips/{$trip->id}/items", [
            'item_type' => 'custom',
            'custom_title' => 'Overflow',
            'day_number' => 1,
        ])->assertStatus(422);

        $trip->delete();

        $this->assertDatabaseCount('trip_items', 0);
    }

    public function test_user_can_add_a_favorite_catalog_item_to_trip(): void
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::create($this->restaurantPayload());
        $trip = Trip::create(['user_id' => $user->id, 'title' => 'Rabat food day']);
        Favorite::create([
            'user_id' => $user->id,
            'favoriteable_id' => $restaurant->id,
            'favoriteable_type' => Restaurant::class,
        ]);

        Sanctum::actingAs($user);

        $this->postJson("/api/trips/{$trip->id}/items", [
            'item_type' => 'restaurant',
            'item_id' => $restaurant->id,
            'day_number' => 1,
        ])->assertOk()
            ->assertJsonPath('data.items.0.item.title', 'Dar Tajine');
    }

    public function test_user_cannot_mutate_another_users_trip(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $trip = Trip::create(['user_id' => $owner->id, 'title' => 'Private']);

        Sanctum::actingAs($other);

        $this->getJson("/api/trips/{$trip->id}")->assertNotFound();
        $this->postJson("/api/trips/{$trip->id}/items", [
            'item_type' => 'custom',
            'custom_title' => 'Nope',
            'day_number' => 1,
        ])->assertNotFound();
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
            'description_fr' => 'Cuisine locale traditionnelle.',
            'description_en' => 'Traditional local cuisine.',
            'city' => 'Rabat',
            'address' => 'Medina',
            'cuisine_type' => 'marocaine',
            'price_range' => 'moyen',
        ], $overrides);
    }
}
