<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Jerome Paulos',
            'email' => 'jero@hey.com'
        ]);

        Location::create(
            [
                'name' => 'Joyce Cummings Center',
                'informal_name' => 'JCC'
            ],
            [
                'name' => 'Tisch Sports and Fitness Center',
                'informal_name' => 'Gym'
            ],
            [
                'name' => 'Tisch Library',
                'informal_name' => 'Tisch Library'
            ]
        );
    }
}
