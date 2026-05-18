<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RiskMitigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_photo_uploads_are_limited_to_two_megabytes(): void
    {
        Storage::fake('public');
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $payload = $this->hotelPayload([
            'photo_files' => [
                $this->fakePng('large-hotel.png', 2049),
            ],
        ]);

        $this->post('/api/admin/hotels', $payload, ['Accept' => 'application/json'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['photo_files.0']);
    }

    public function test_admin_can_upload_valid_photo_and_api_returns_public_url(): void
    {
        Storage::fake('public');
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $payload = $this->hotelPayload([
            'photos' => ['https://example.test/existing.jpg'],
            'photo_files' => [
                $this->fakePng('hotel.png'),
            ],
        ]);

        $response = $this->post('/api/admin/hotels', $payload, ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('data.photos.0', 'https://example.test/existing.jpg');

        $hotelId = $response->json('data.id');
        $uploadedUrl = $response->json('data.photos.1');

        $this->assertStringStartsWith('/storage/uploads/hotels/', $uploadedUrl);
        $this->assertCount(1, Storage::disk('public')->allFiles('uploads/hotels'));

        $this->getJson("/api/hotels/{$hotelId}")
            ->assertOk()
            ->assertJsonPath('data.photos.1', $uploadedUrl);
    }

    public function test_photo_urls_must_be_valid_urls(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $this->postJson('/api/admin/hotels', $this->hotelPayload([
            'photos' => ['not-a-url'],
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['photos.0']);
    }

    public function test_admin_upload_endpoint_returns_public_photo_url(): void
    {
        Storage::fake('public');
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $response = $this->post('/api/admin/upload', [
            'directory' => 'hotels',
            'photo' => $this->fakePng('hotel.png'),
        ], ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('path', fn (string $path) => str_starts_with($path, 'uploads/hotels/'));

        Storage::disk('public')->assertExists($response->json('path'));
    }

    private function hotelPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Risk Hotel',
            'description_fr' => 'Hotel de validation des risques.',
            'description_en' => 'Risk validation hotel.',
            'city' => 'Casablanca',
            'district' => 'Centre',
            'stars' => 4,
            'price_min' => 900,
            'price_max' => 1400,
            'currency' => 'MAD',
        ], $overrides);
    }

    private function fakePng(string $name, int $kilobytes = 1): UploadedFile
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=');
        $targetBytes = max($kilobytes * 1024, strlen($png));

        return UploadedFile::fake()->createWithContent(
            $name,
            $png.str_repeat('0', $targetBytes - strlen($png))
        );
    }
}
