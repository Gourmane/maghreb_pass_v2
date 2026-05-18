<?php

namespace Tests\Feature;

use App\Models\Attraction;
use App\Models\FootballMatch;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AcceptanceCriteriaTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seed_data_matches_mvp_requirements(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(8, FootballMatch::count());
        $this->assertSame(10, Hotel::count());
        $this->assertSame(10, Restaurant::count());
        $this->assertSame(10, Attraction::count());
        $this->assertSame(1, User::where('role', 'admin')->count());
        $this->assertSame(2, User::where('role', 'tourist')->count());
    }

    public function test_demo_seed_images_use_direct_image_urls(): void
    {
        $this->seed(DatabaseSeeder::class);

        User::query()
            ->whereNotNull('avatar_url')
            ->pluck('avatar_url')
            ->each(fn (string $url) => $this->assertDirectImageUrl($url));

        FootballMatch::query()
            ->get(['team_home_code', 'team_home_flag_url', 'team_away_code', 'team_away_flag_url'])
            ->each(function (FootballMatch $match): void {
                $this->assertNotEmpty($match->team_home_code);
                $this->assertNotEmpty($match->team_away_code);
                $this->assertDirectImageUrl($match->team_home_flag_url);
                $this->assertDirectImageUrl($match->team_away_flag_url);
            });

        collect([Hotel::class, Restaurant::class, Attraction::class])
            ->each(function (string $model): void {
                $model::query()
                    ->pluck('photos')
                    ->flatten()
                    ->each(fn (string $url) => $this->assertDirectImageUrl($url));
            });
    }

    public function test_visitor_can_browse_public_catalog_without_account(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->getJson('/api/matches')->assertOk()->assertJsonCount(8, 'data');
        $this->getJson('/api/hotels')->assertOk()->assertJsonCount(10, 'data');
        $this->getJson('/api/restaurants')->assertOk()->assertJsonCount(10, 'data');
        $this->getJson('/api/attractions')->assertOk()->assertJsonCount(10, 'data');
    }

    public function test_city_filter_works_on_each_public_module(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->getJson('/api/matches?city=Casablanca')
            ->assertOk()
            ->assertJsonPath('data.0.city', 'Casablanca');

        $this->getJson('/api/hotels?city=Rabat')
            ->assertOk()
            ->assertJsonPath('data.0.city', 'Rabat');

        $this->getJson('/api/restaurants?city=Tanger')
            ->assertOk()
            ->assertJsonPath('data.0.city', 'Tanger');

        $this->getJson('/api/attractions?city=Fes')
            ->assertOk()
            ->assertJsonPath('data.0.city', 'Fes');
    }

    public function test_user_can_create_account_login_and_manage_favorites(): void
    {
        $this->seed(DatabaseSeeder::class);

        $registerResponse = $this->postJson('/api/auth/register', [
            'name' => 'Acceptance Tourist',
            'email' => 'acceptance.tourist@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'preferred_language' => 'fr',
        ])
            ->assertCreated()
            ->assertJsonStructure(['token', 'user']);

        $token = $registerResponse->json('token');
        $hotel = Hotel::firstOrFail();

        $favoriteResponse = $this
            ->withToken($token)
            ->postJson('/api/favorites', [
                'type' => 'hotel',
                'id' => $hotel->id,
            ])
            ->assertCreated()
            ->assertJsonPath('favorite.item.id', $hotel->id);

        $this
            ->withToken($token)
            ->getJson('/api/favorites')
            ->assertOk()
            ->assertJsonCount(1, 'data.hotels');

        $this
            ->withToken($token)
            ->deleteJson('/api/favorites/'.$favoriteResponse->json('favorite.id'))
            ->assertOk();

        $this
            ->postJson('/api/auth/login', [
                'email' => 'acceptance.tourist@example.com',
                'password' => 'password123',
            ])
            ->assertOk()
            ->assertJsonPath('user.email', 'acceptance.tourist@example.com');
    }

    public function test_admin_can_manage_all_content_types(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Sanctum::actingAs($admin);

        $hotelId = $this->postJson('/api/admin/hotels', $this->hotelPayload())
            ->assertCreated()
            ->assertJsonPath('data.name', 'Acceptance Hotel')
            ->json('data.id');

        $restaurantId = $this->postJson('/api/admin/restaurants', $this->restaurantPayload())
            ->assertCreated()
            ->assertJsonPath('data.name', 'Acceptance Restaurant')
            ->json('data.id');

        $attractionId = $this->postJson('/api/admin/attractions', $this->attractionPayload())
            ->assertCreated()
            ->assertJsonPath('data.name', 'Acceptance Attraction')
            ->json('data.id');

        $this->putJson("/api/admin/hotels/{$hotelId}", $this->hotelPayload(['city' => 'Rabat']))
            ->assertOk()
            ->assertJsonPath('data.city', 'Rabat');

        $this->putJson("/api/admin/restaurants/{$restaurantId}", $this->restaurantPayload(['city' => 'Tanger']))
            ->assertOk()
            ->assertJsonPath('data.city', 'Tanger');

        $this->putJson("/api/admin/attractions/{$attractionId}", $this->attractionPayload(['city' => 'Fes']))
            ->assertOk()
            ->assertJsonPath('data.city', 'Fes');

        $this->deleteJson("/api/admin/hotels/{$hotelId}")->assertNoContent();
        $this->deleteJson("/api/admin/restaurants/{$restaurantId}")->assertNoContent();
        $this->deleteJson("/api/admin/attractions/{$attractionId}")->assertNoContent();
    }

    public function test_bilingual_content_and_photos_are_returned_by_detail_pages(): void
    {
        $hotel = Hotel::create($this->hotelPayload());

        $this->getJson("/api/hotels/{$hotel->id}")
            ->assertOk()
            ->assertJsonPath('data.description_fr', 'Description francaise.')
            ->assertJsonPath('data.description_en', 'English description.')
            ->assertJsonPath('data.photos.0', 'https://example.test/hotel.jpg');
    }

    private function hotelPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Acceptance Hotel',
            'description_fr' => 'Description francaise.',
            'description_en' => 'English description.',
            'city' => 'Casablanca',
            'district' => 'Centre',
            'stars' => 4,
            'price_min' => 900,
            'price_max' => 1400,
            'currency' => 'MAD',
            'photos' => ['https://example.test/hotel.jpg'],
        ], $overrides);
    }

    private function restaurantPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Acceptance Restaurant',
            'description_fr' => 'Restaurant de demonstration.',
            'description_en' => 'Demo restaurant.',
            'city' => 'Casablanca',
            'address' => 'Centre',
            'cuisine_type' => 'Marocaine',
            'price_range' => 'moyen',
            'photos' => ['https://example.test/restaurant.jpg'],
        ], $overrides);
    }

    private function attractionPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Acceptance Attraction',
            'description_fr' => 'Attraction de demonstration.',
            'description_en' => 'Demo attraction.',
            'city' => 'Casablanca',
            'address' => 'Centre',
            'category' => 'Musee',
            'entry_price' => 50,
            'opening_hours' => '09:00-18:00',
            'photos' => ['https://example.test/attraction.jpg'],
        ], $overrides);
    }

    private function assertDirectImageUrl(?string $url): void
    {
        $this->assertNotEmpty($url);
        $this->assertStringNotContainsString('commons.wikimedia.org/wiki', $url);
        $this->assertMatchesRegularExpression(
            '#^https://(upload\.wikimedia\.org/.+\.(?:jpg|jpeg|png|svg)|flagcdn\.com/.+\.png)$#i',
            $url
        );
    }
}
