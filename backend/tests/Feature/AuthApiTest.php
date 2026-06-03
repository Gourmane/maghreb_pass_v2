<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_tourist_can_register_and_receive_token(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Sara Benali',
            'email' => 'sara@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'preferred_language' => 'en',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.email', 'sara@example.com')
            ->assertJsonPath('user.role', 'tourist')
            ->assertJsonStructure(['token'])
            ->assertCookie('maghrebpass_token');

        $this->assertDatabaseHas('users', [
            'email' => 'sara@example.com',
            'role' => 'tourist',
            'preferred_language' => 'en',
            'is_active' => true,
        ]);
    }

    public function test_user_can_login_and_read_profile(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
            'role' => 'tourist',
            'is_active' => true,
        ]);

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ])
            ->assertOk()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.email', $user->email)
            ->assertJsonStructure(['token'])
            ->assertCookie('maghrebpass_token');

        Sanctum::actingAs($user);

        $this->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.email', $user->email);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
            'is_active' => false,
        ]);

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ])
            ->assertForbidden()
            ->assertJsonPath('message', 'Ce compte est desactive.');
    }

    public function test_user_can_update_profile_language_and_avatar(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'preferred_language' => 'fr',
            'avatar_url' => null,
        ]);

        Sanctum::actingAs($user);

        $this->putJson('/api/auth/profile', [
            'name' => 'New Name',
            'preferred_language' => 'en',
            'avatar_url' => 'https://upload.wikimedia.org/wikipedia/commons/1/12/User_icon_2.svg',
        ])
            ->assertOk()
            ->assertJsonPath('user.name', 'New Name')
            ->assertJsonPath('user.preferred_language', 'en')
            ->assertJsonPath('user.avatar_url', 'https://upload.wikimedia.org/wikipedia/commons/1/12/User_icon_2.svg');
    }

    public function test_user_can_upload_profile_avatar_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'name' => 'Old Name',
            'preferred_language' => 'fr',
            'avatar_url' => null,
        ]);

        Sanctum::actingAs($user);

        $response = $this->post('/api/auth/profile', [
            'name' => 'New Name',
            'preferred_language' => 'fr',
            'avatar_file' => UploadedFile::fake()->createWithContent(
                'avatar.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=')
            ),
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('user.name', 'New Name');

        $avatarUrl = $response->json('user.avatar_url');

        $this->assertNotNull($avatarUrl);
        $this->assertStringContainsString('/storage/uploads/avatars/', $avatarUrl);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', parse_url($avatarUrl, PHP_URL_PATH)));
    }

    public function test_password_reset_email_can_be_requested(): void
    {
        Password::shouldReceive('sendResetLink')
            ->once()
            ->andReturn(Password::RESET_LINK_SENT);

        $this->postJson('/api/auth/forgot-password', [
            'email' => 'tourist@example.com',
        ])->assertOk();
    }
}
