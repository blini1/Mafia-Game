<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Player;
use App\Models\Game;
use App\Models\Role;
use App\Models\User;

class PlayerFactory extends Factory
{
    protected $model = Player::class;

    public function definition()
    {
        // Ensure that there are Game, Role, and User instances available
        $game = Game::factory()->create();
        $role = Role::factory()->create();
        $user = User::factory()->create();

        return [
            'name' => $this->faker->name,
            'game_id' => $game->id,
            'user_id' => $user->id,
            'role_id' => $role->id,
            'status' => $this->faker->randomElement(['alive', 'dead']),
        ];
    }
}
