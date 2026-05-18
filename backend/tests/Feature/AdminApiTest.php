<?php

namespace Tests\Feature;

use App\Models\FootballMatch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_routes_require_admin_role(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'tourist']));

        $this->getJson('/api/admin/stats')
            ->assertForbidden()
            ->assertJsonPath('message', 'Acces refuse.');
    }

    public function test_admin_can_create_update_and_delete_match(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $createResponse = $this->postJson('/api/admin/matches', $this->matchPayload([
            'team_home' => 'Morocco',
            'team_away' => 'France',
            'team_away_code' => 'FRA',
            'team_away_flag_url' => 'https://flagcdn.com/w320/fr.png',
            'city' => 'Casablanca',
        ]));

        $createResponse
            ->assertCreated()
            ->assertJsonPath('data.team_home', 'Morocco')
            ->assertJsonPath('data.team_home_code', 'MAR')
            ->assertJsonPath('data.team_home_flag_url', 'https://flagcdn.com/w320/ma.png')
            ->assertJsonPath('data.team_away', 'France')
            ->assertJsonPath('data.team_away_code', 'FRA')
            ->assertJsonPath('data.team_away_flag_url', 'https://flagcdn.com/w320/fr.png')
            ->assertJsonPath('data.city', 'Casablanca');

        $matchId = $createResponse->json('data.id');

        $this->putJson("/api/admin/matches/{$matchId}", $this->matchPayload([
            'team_home' => 'Morocco',
            'team_away' => 'France',
            'city' => 'Rabat',
            'status' => 'live',
        ]))
            ->assertOk()
            ->assertJsonPath('data.city', 'Rabat')
            ->assertJsonPath('data.status', 'live');

        $this->assertDatabaseHas('matches', [
            'id' => $matchId,
            'city' => 'Rabat',
            'status' => 'live',
        ]);

        $this->deleteJson("/api/admin/matches/{$matchId}")
            ->assertNoContent();

        $this->assertDatabaseMissing('matches', ['id' => $matchId]);
    }

    public function test_admin_can_list_stats_and_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['role' => 'tourist']);
        FootballMatch::create($this->matchPayload());

        Sanctum::actingAs($admin);

        $this->getJson('/api/admin/stats')
            ->assertOk()
            ->assertJsonStructure([
                'users',
                'matches',
                'hotels',
                'restaurants',
                'attractions',
                'favorites',
                'tourists',
                'admins',
            ])
            ->assertJsonPath('users', 2)
            ->assertJsonPath('matches', 1);

        $this->getJson('/api/admin/users')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    private function matchPayload(array $overrides = []): array
    {
        return array_merge([
            'team_home' => 'Morocco',
            'team_home_code' => 'MAR',
            'team_home_flag_url' => 'https://flagcdn.com/w320/ma.png',
            'team_away' => 'Portugal',
            'team_away_code' => 'POR',
            'team_away_flag_url' => 'https://flagcdn.com/w320/pt.png',
            'match_date' => '2026-06-12',
            'match_time' => '20:00',
            'stadium' => 'Grand Stade',
            'city' => 'Casablanca',
            'group_name' => 'A',
            'phase' => 'group',
            'status' => 'upcoming',
        ], $overrides);
    }
}
