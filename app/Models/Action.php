<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory;
    protected $fillable = ['game_id', 'player_id', 'target_player_id', 'type', 'phase', 'result'];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function targetPlayer()
    {
        return $this->belongsTo(Player::class, 'target_player_id');
    }
}
