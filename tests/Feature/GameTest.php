<?php

namespace Tests\Feature;

use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Game;

class GameTest extends TestCase
{
    /** @test */
    public function a_game_can_be_started()
    {
        $response = $this->post('/game/start');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'gameId', 'userRoleId', 'userRoleName', 'nightPhaseResult', 'userPlayerId'
            ]);

        $this->assertDatabaseHas('games', ['status' => 'in_progress']);
    }

    /** @test */
    public function night_phase_can_be_processed()
    {
        $game = Game::factory()->create(['status' => 'in_progress']);

        $response = $this->post('/game/night-phase', ['game_id' => $game->id]);

        $response->assertStatus(200)
            ->assertJsonStructure(['nightPhaseResult']);
    }

    /** @test */
    public function a_vote_can_be_cast()
    {
        $game = Game::factory()->create(['status' => 'in_progress']);
        $player = Player::factory()->create(['game_id' => $game->id, 'status' => 'alive']);

        // Simulate a user login
        $this->actingAs($player->user);

        $response = $this->post('/game/vote', [
            'game_id' => $game->id,
            'suspect_id' => $player->id
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['dayPhaseResult']);
    }

    /** @test */
    public function it_retrieves_alive_players()
    {
        $game = Game::factory()->create(['status' => 'in_progress']);
        Player::factory()->count(5)->create(['game_id' => $game->id, 'status' => 'alive']);

        $response = $this->get("/game/alive-players?game_id={$game->id}");

        $response->assertStatus(200)
            ->assertJsonCount(5, 'players');
    }
}
