<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\PackageItem;
use App\Models\Restaurant;
use App\Models\Attraction;
use App\Models\FootballMatch;
use App\Models\Trip;
use App\Models\TripItem;
use App\Models\TravelPackage;
use App\Models\User;
use Database\Seeders\AttractionSeeder;
use Database\Seeders\HotelSeeder;
use Database\Seeders\MatchSeeder;
use Database\Seeders\PackageSeeder;
use Database\Seeders\RestaurantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PackageApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_browse_active_packages_with_day_items(): void
    {
        $hotel = Hotel::create($this->hotelPayload());
        $package = TravelPackage::create($this->packagePayload());
        $package->items()->create([
            'item_type' => 'hotel',
            'item_id' => $hotel->id,
            'day_number' => 1,
            'sort_order' => 1,
        ]);
        TravelPackage::create($this->packagePayload(['title_fr' => 'Package cache', 'title_en' => 'Hidden package', 'is_active' => false]));

        $this->getJson('/api/packages')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title_fr', 'Weekend Casablanca')
            ->assertJsonPath('data.0.title_en', 'Casablanca Weekend')
            ->assertJsonPath('data.0.title', 'Weekend Casablanca');

        $this->getJson("/api/packages/{$package->id}")
            ->assertOk()
            ->assertJsonPath('data.items.0.item.title', 'Hotel Atlas');
    }

    public function test_admin_can_create_package_add_items_and_reorder_them(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $response = $this->postJson('/api/admin/packages', $this->packagePayload(['title_fr' => 'Plan Rabat', 'title_en' => 'Rabat Plan']))
            ->assertCreated()
            ->assertJsonPath('data.title_fr', 'Plan Rabat')
            ->assertJsonPath('data.title_en', 'Rabat Plan');

        $packageId = $response->json('data.id');

        $this->postJson("/api/admin/packages/{$packageId}/items", [
            'item_type' => 'custom',
            'custom_title' => 'Arrival',
            'custom_description' => 'Hotel check-in.',
            'day_number' => 1,
        ])->assertOk()
            ->assertJsonPath('data.items.0.item.title', 'Arrival');

        $second = $this->postJson("/api/admin/packages/{$packageId}/items", [
            'item_type' => 'custom',
            'custom_title' => 'Dinner',
            'day_number' => 1,
        ])->assertOk();

        $itemId = $second->json('data.items.1.id');

        $this->putJson("/api/admin/packages/{$packageId}/items/{$itemId}/move/up")
            ->assertOk()
            ->assertJsonPath('data.items.0.item.title', 'Dinner');
    }

    public function test_package_items_are_limited_to_thirty(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $package = TravelPackage::create($this->packagePayload());

        for ($index = 1; $index <= 30; $index++) {
            PackageItem::create([
                'package_id' => $package->id,
                'item_type' => 'custom',
                'custom_title' => "Stop {$index}",
                'day_number' => 1,
                'sort_order' => $index,
            ]);
        }

        $this->postJson("/api/admin/packages/{$package->id}/items", [
            'item_type' => 'custom',
            'custom_title' => 'Overflow',
            'day_number' => 1,
        ])->assertStatus(422);
    }

    public function test_admin_can_only_add_same_city_catalog_items_to_package(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $package = TravelPackage::create($this->packagePayload(['city' => 'Casablanca']));
        $casablancaHotel = Hotel::create($this->hotelPayload(['city' => 'Casablanca']));
        $rabatHotel = Hotel::create($this->hotelPayload(['name' => 'Rabat Hotel', 'city' => 'Rabat']));

        $this->postJson("/api/admin/packages/{$package->id}/items", [
            'item_type' => 'hotel',
            'item_id' => $casablancaHotel->id,
            'day_number' => 1,
        ])->assertOk()
            ->assertJsonPath('data.items.0.item.title', 'Hotel Atlas');

        $this->postJson("/api/admin/packages/{$package->id}/items", [
            'item_type' => 'hotel',
            'item_id' => $rabatHotel->id,
            'day_number' => 1,
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['item_id']);
    }

    public function test_public_package_search_checks_french_and_english_titles(): void
    {
        TravelPackage::create($this->packagePayload([
            'title_fr' => 'Week-end football Casablanca',
            'title_en' => 'Casablanca Football Weekend',
        ]));

        $this->getJson('/api/packages?search=Football')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title_en', 'Casablanca Football Weekend');

        $this->getJson('/api/packages?search=Week-end')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title_fr', 'Week-end football Casablanca');
    }

    public function test_admin_cannot_delete_catalog_item_used_by_package(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $hotel = Hotel::create($this->hotelPayload());
        $package = TravelPackage::create($this->packagePayload());
        $package->items()->create([
            'item_type' => 'hotel',
            'item_id' => $hotel->id,
            'day_number' => 1,
            'sort_order' => 1,
        ]);

        $this->deleteJson("/api/admin/hotels/{$hotel->id}")
            ->assertStatus(409);
    }

    public function test_admin_cannot_delete_catalog_items_used_by_trip_items(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $owner = User::factory()->create();
        $trip = Trip::create(['user_id' => $owner->id, 'title' => 'Protected trip']);
        $hotel = Hotel::create($this->hotelPayload());
        $restaurant = Restaurant::create($this->restaurantPayload());
        $attraction = Attraction::create($this->attractionPayload());
        $match = FootballMatch::create($this->matchPayload());

        foreach ([
            ['hotel', $hotel->id],
            ['restaurant', $restaurant->id],
            ['attraction', $attraction->id],
            ['match', $match->id],
        ] as $index => [$type, $id]) {
            TripItem::create([
                'trip_id' => $trip->id,
                'item_type' => $type,
                'item_id' => $id,
                'day_number' => 1,
                'sort_order' => $index + 1,
            ]);
        }

        $this->deleteJson("/api/admin/hotels/{$hotel->id}")->assertStatus(409);
        $this->deleteJson("/api/admin/restaurants/{$restaurant->id}")->assertStatus(409);
        $this->deleteJson("/api/admin/attractions/{$attraction->id}")->assertStatus(409);
        $this->deleteJson("/api/admin/matches/{$match->id}")->assertStatus(409);
    }

    public function test_seeded_casablanca_package_includes_a_match_item(): void
    {
        $this->seed([
            MatchSeeder::class,
            HotelSeeder::class,
            RestaurantSeeder::class,
            AttractionSeeder::class,
            PackageSeeder::class,
        ]);

        $package = TravelPackage::where('title_en', 'Casablanca Match Weekend')->firstOrFail();

        $this->assertDatabaseHas('package_items', [
            'package_id' => $package->id,
            'item_type' => 'match',
        ]);
    }

    private function packagePayload(array $overrides = []): array
    {
        return array_merge([
            'title_fr' => 'Weekend Casablanca',
            'title_en' => 'Casablanca Weekend',
            'description_fr' => 'Programme touristique.',
            'description_en' => 'Travel program.',
            'city' => 'Casablanca',
            'price_min' => 1200,
            'price_max' => 2400,
            'currency' => 'MAD',
            'image_url' => 'https://example.test/package.jpg',
            'is_active' => true,
        ], $overrides);
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
            'city' => 'Casablanca',
            'address' => 'Medina',
            'cuisine_type' => 'marocaine',
            'price_range' => 'moyen',
        ], $overrides);
    }

    private function attractionPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Mosquee Hassan II',
            'description_fr' => 'Site culturel majeur.',
            'description_en' => 'Major cultural site.',
            'city' => 'Casablanca',
            'address' => 'Boulevard Sidi Mohammed Ben Abdallah',
            'category' => 'culture',
            'entry_price' => 0,
        ], $overrides);
    }

    private function matchPayload(array $overrides = []): array
    {
        return array_merge([
            'team_home' => 'Morocco',
            'team_away' => 'Portugal',
            'match_date' => '2030-06-14',
            'match_time' => '20:00',
            'stadium' => 'Grand Stade Hassan II',
            'city' => 'Casablanca',
            'group_name' => 'A',
            'phase' => 'group',
            'status' => 'upcoming',
        ], $overrides);
    }
}
