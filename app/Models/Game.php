<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = ['status', 'winner'];

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function actions()
    {
        return $this->hasMany(Action::class);
    }
}
