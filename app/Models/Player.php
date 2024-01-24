<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = ['name','game_id', 'user_id', 'role_id', 'status'];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function actions()
    {
        return $this->hasMany(Action::class, 'player_id');
    }
}
