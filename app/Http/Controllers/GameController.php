<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Player;
use App\Models\Role;
use App\Services\GameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    protected $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function start(Request $request)
    {
        $game = Game::create(['status' => 'in_progress']);

        $userRole = Role::inRandomOrder()->first();
        $userPlayer = Player::create([
            'name' => Auth::user()->name,
            'game_id' => $game->id,
            'user_id' => Auth::id(),
            'role_id' => $userRole->id,
            'status' => 'alive'
        ]);

        $this->assignRolesToAIPlayers($game);

        // Simulate the first night phase
        $nightPhaseResult = $this->gameService->simulateNightPhase($game);

        return response()->json([
            'gameId' => $game->id,
            'userRoleId' => $userRole->id,
            'userRoleName' => $userRole->name,
            'nightPhaseResult' => $nightPhaseResult,
            'userPlayerId' => $userPlayer->id,
        ]);
    }

    public function nightPhase(Request $request)
    {
        $gameId = $request->input('game_id');
        $game = Game::find($gameId);

        if (!$game || $game->status !== 'in_progress') {
            return response()->json(['message' => 'Game not found or not in progress'], 404);
        }

        $nightPhaseResult = $this->gameService->simulateNightPhase($game);

        // Check if the game has a winner
        $winner = $this->gameService->determineWinner($game);

        if ($winner !== 'No Winner') {
            $game->update(['status' => 'finished', 'winner' => $winner]);

            // Including the winner in the response
            return response()->json([
                'nightPhaseResult' => $nightPhaseResult,
                'gameOver' => true,
                'winner' => $winner
            ]);
        }

        return response()->json(['nightPhaseResult' => $nightPhaseResult]);
    }

    public function vote(Request $request)
    {
        $gameId = $request->input('game_id');
        $suspectId = $request->input('suspect_id');

        $game = Game::find($gameId);
        if (!$game || $game->status !== 'in_progress') {
            return response()->json(['message' => 'Game not found or not in progress'], 404);
        }

        // Ensure the logged-in user is part of the game and is alive
        $userPlayer = Player::where('game_id', $gameId)
            ->where('user_id', Auth::id())
            ->where('status', 'alive')
            ->first();

        if (!$userPlayer) {
            return response()->json(['message' => 'User not part of the game or already eliminated'], 403);
        }

        // Process the vote
        $dayPhaseResult = $this->gameService->simulateDayPhase($game, $userPlayer, $suspectId);

        $winner = $this->gameService->determineWinner($game);

        if ($winner !== 'No Winner') {
            $game->update(['status' => 'finished', 'winner' => $winner]);
            return response()->json(['dayPhaseResult' => $dayPhaseResult, 'gameOver' => true, 'winner' => $winner]);
        }

        return response()->json(['dayPhaseResult' => $dayPhaseResult]);
    }

    public function getAlivePlayers(Request $request)
    {
        $gameId = $request->input('game_id');
        $alivePlayers = Player::where('game_id', $gameId)
            ->where('status', 'alive')
            ->whereNull('user_id')
            ->get(['id', 'name']);
        return response()->json(['players' => $alivePlayers]);
    }

    private function assignRolesToAIPlayers($game)
    {
        $assignedRoles = [
            'Mafia' => 2,
            'Doctor' => 1,
            'Detective' => 1,
        ];

        $aiPlayerNames = [
            'Sneaky Pete', 'Mysterious Max', 'Crafty Clara', 'Shifty Steve',
            'Cunning Cassandra', 'Tricky Tom', 'Devious Dave', 'Clever Chloe',
            'Wily Wendy', 'Foxy Fred'
        ];

        shuffle($aiPlayerNames);

        foreach ($assignedRoles as $roleName => $count) {
            $role = Role::where('name', $roleName)->first();
            for ($i = 0; $i < $count; $i++) {
                $name = array_pop($aiPlayerNames);
                Player::create([
                    'name' => $name,
                    'game_id' => $game->id,
                    'role_id' => $role->id,
                    'status' => 'alive'
                ]);
            }
        }

        $villagerRole = Role::where('name', 'Villager')->first();
        $totalPlayers = 10;
        $currentPlayerCount = Player::where('game_id', $game->id)->count();

        for ($i = $currentPlayerCount; $i < $totalPlayers; $i++) {
            $name = array_pop($aiPlayerNames);
            Player::create([
                'name' => $name,
                'game_id' => $game->id,
                'role_id' => $villagerRole->id,
                'status' => 'alive'
            ]);
        }
    }
}
