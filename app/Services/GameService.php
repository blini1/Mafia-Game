<?php

namespace App\Services;

use App\Models\Action;
use App\Models\Game;
use App\Models\Player;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;


class GameService
{
    public function simulateNightPhase($game) {
        $nonMafiaPlayers = Player::where('game_id', $game->id)
            ->where('status', 'alive')
            ->whereHas('role', function($query) {
                $query->where('name', '!=', 'Mafia');
            })
            ->get();

        $target = $nonMafiaPlayers->random();

        $savedByDoctor = $this->simulateDoctorAction($game, $target);

        if (!$savedByDoctor) {
            $target->update(['status' => 'dead']);
            $eliminatedPlayer = $target;
        } else {
            $eliminatedPlayer = null;
        }

        $mafiaRoleId = Role::where('name', 'Mafia')->first()->id;

        $mafiaPlayer = Player::where('game_id', $game->id)
            ->where('role_id', $mafiaRoleId)
            ->first();

        Action::create([
            'game_id' => $game->id,
            'player_id' => $mafiaPlayer->id,
            'target_player_id' => $target->id,
            'type' => 'kill',
            'phase' => 'night',
            'result' => $savedByDoctor ? 'saved' : 'eliminated'
        ]);

        $userEliminated = false;
        if ($eliminatedPlayer && Auth::check()) {
            $userEliminated = $eliminatedPlayer->id == Auth::id();
        }

        return [
            'eliminatedPlayer' => $eliminatedPlayer ? $eliminatedPlayer->id : null,
            'savedByDoctor' => $savedByDoctor,
            'userEliminated' => $userEliminated,
        ];
    }

    public function simulateDayPhase($game, $votingPlayer, $suspectId)
    {
        // Record the user's vote
        $this->recordVote($game->id, $votingPlayer->id, $suspectId);

        // Simulate votes for AI players
        $alivePlayers = Player::where('game_id', $game->id)
            ->where('status', 'alive')
            ->get();

        foreach ($alivePlayers as $player) {
            if ($player->id != $votingPlayer->id) { // Exclude the voting user
                $suspect = $this->chooseSuspect($alivePlayers, $player);
                $this->recordVote($game->id, $player->id, $suspect->id);
            }
        }

        // Determine the outcome of the voting
        $eliminatedPlayerId = $this->determineVotingOutcome($game);
        $eliminatedPlayerName = $this->updatePlayerStatus($eliminatedPlayerId);

        // Return the results of the day phase
        return [
            'eliminatedPlayer' => $eliminatedPlayerId,
            'userEliminated' => $eliminatedPlayerId == Auth::user()->id,
        ];
    }

    public function chooseSuspect($players, $votingPlayer) {
        $suspects = $players->where('id', '!=', $votingPlayer->id);
        return $suspects->random();
    }

    public function simulateDoctorAction($game, $target) {
        $doctor = Player::where('game_id', $game->id)
            ->where('status', 'alive')
            ->whereHas('role', function($query) {
                $query->where('name', 'Doctor');
            })
            ->first();

        if ($doctor) {
            return rand(0, 1) === 1;
        }

        return false;
    }

    public function determineWinner($game)
    {
        $mafiaCount = Player::where('game_id', $game->id)
            ->where('status', 'alive')
            ->whereHas('role', function($query) {
                $query->where('name', 'Mafia');
            })
            ->count();

        $villagerCount = Player::where('game_id', $game->id)
                ->where('status', 'alive')
                ->count() - $mafiaCount;

        if ($mafiaCount == 0) {
            $game->status = 'finished';
            $game->winner = 'Villagers';
            $game->save();
            return 'Villagers';
        } elseif ($mafiaCount >= $villagerCount) {
            $game->status = 'finished';
            $game->winner = 'Mafia';
            $game->save();
            return 'Mafia';
        }

        return 'No Winner';
    }

    private function determineVotingOutcome($game)
    {
        $voteResults = Action::where('game_id', $game->id)
            ->where('phase', 'day')
            ->where('type', 'vote')
            ->get()
            ->groupBy('target_player_id')
            ->map->count();

        return $voteResults->keys()->max();
    }

    private function updatePlayerStatus($playerId)
    {
        $player = Player::find($playerId);
        if ($player) {
            $player->update(['status' => 'dead']);
        }

        return $player;
    }

    private function recordVote($gameId, $voterId, $suspectId)
    {
        Action::create([
            'game_id' => $gameId,
            'player_id' => $voterId,
            'target_player_id' => $suspectId,
            'type' => 'vote',
            'phase' => 'day',
            'result' => 'cast vote'
        ]);
    }
}
