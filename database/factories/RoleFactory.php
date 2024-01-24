<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition()
    {
        return [
            'name' => 'Villager',
            'description' => 'Villager role',
        ];
    }

    public function mafia()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Mafia',
                'description' => 'Mafia role',
            ];
        });
    }

    public function detective()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Detective',
                'description' => 'Detective role',
            ];
        });
    }

    public function doctor()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Doctor',
                'description' => 'Doctor role',
            ];
        });
    }
}
