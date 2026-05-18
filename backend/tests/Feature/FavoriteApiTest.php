<?php

namespace Tests\Feature;

use App\Models\Favorite;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FavoriteApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_favorites(): void
    {
        $this->getJson('/api/favorites')->assertUnauthorized();
        $this->postJson('/api/favorites', ['type' => 'hotel', 'id' => 1])->assertUnauthorized();
    }

    public function test_user_can_add_list_and_remove_favorite(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::create($this->hotelPayload());

        Sanctum::actingAs($user);

        $this->postJson('/api/favorites', [
            'type' => 'hotel',
            'id' => $hotel->id,
        ])
            ->assertCreated()
            ->assertJsonPath('message', 'Favori ajoute.')
            ->assertJsonPath('favorite.type', 'hotel')
            ->assertJsonPath('favorite.item.id', $hotel->id);

        $this->postJson('/api/favorites', [
            'type' => 'hotel',
            'id' => $hotel->id,
        ])
            ->assertOk()
            ->assertJsonPath('message', 'Favori deja existant.');

        $favorite = Favorite::where('user_id', $user->id)->firstOrFail();

        $this->getJson('/api/favorites')
            ->assertOk()
            ->assertJsonCount(1, 'data.hotels')
            ->assertJsonPath('data.hotels.0.id', $favorite->id)
            ->assertJsonPath('data.restaurants', [])
            ->assertJsonPath('data.attractions', []);

        $this->deleteJson("/api/favorites/{$favorite->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Favori retire.');

        $this->assertDatabaseMissing('favorites', ['id' => $favorite->id]);
    }

    public function test_user_cannot_delete_another_users_favorite(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $restaurant = Restaurant::create($this->restaurantPayload());

        $favorite = Favorite::create([
            'user_id' => $owner->id,
            'favoriteable_id' => $restaurant->id,
            'favoriteable_type' => Restaurant::class,
        ]);

        Sanctum::actingAs($otherUser);

        $this->deleteJson("/api/favorites/{$favorite->id}")
            ->assertNotFound()
            ->assertJsonPath('message', 'Favori introuvable.');

        $this->assertDatabaseHas('favorites', ['id' => $favorite->id]);
    }

    public function test_adding_missing_favoriteable_returns_not_found(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/favorites', [
            'type' => 'hotel',
            'id' => 999,
        ])
            ->assertNotFound()
            ->assertJsonPath('message', 'Element introuvable.');
    }

    private function hotelPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Hotel Atlas',
            'description_fr' => 'Hotel confortable proche du centre.',
            'description_en' => 'Comfortable hotel near the center.',
            'city' => 'Marrakech',
            'district' => 'Gueliz',
            'stars' => 5,
            'price_min' => 900,
            'price_max' => 1600,
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
