<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\PackageItem;
use App\Models\TravelPackage;
use App\Models\User;
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
        TravelPackage::create($this->packagePayload(['title' => 'Hidden package', 'is_active' => false]));

        $this->getJson('/api/packages')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Casablanca Weekend');

        $this->getJson("/api/packages/{$package->id}")
            ->assertOk()
            ->assertJsonPath('data.items.0.item.title', 'Hotel Atlas');
    }

    public function test_admin_can_create_package_add_items_and_reorder_them(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $response = $this->postJson('/api/admin/packages', $this->packagePayload(['title' => 'Rabat Plan']))
            ->assertCreated()
            ->assertJsonPath('data.title', 'Rabat Plan');

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

    private function packagePayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Casablanca Weekend',
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
}
