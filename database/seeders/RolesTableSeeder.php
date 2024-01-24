<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $roles = [
            ['name' => 'Mafia', 'description' => 'Mafia role'],
            ['name' => 'Detective', 'description' => 'Detective role'],
            ['name' => 'Doctor', 'description' => 'Doctor role'],
            ['name' => 'Villager', 'description' => 'Villager role'],
        ];

        DB::table('roles')->insert($roles);
    }
}
