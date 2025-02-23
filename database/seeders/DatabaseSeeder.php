<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Monitor;
use App\Models\Scan;
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

        $locations = [
            [
                'name' => 'Joyce Cummings Center',
                'informal_name' => 'JCC',
                'order' => 0
            ],
            [
                'name' => 'Tisch Sports and Fitness Center',
                'informal_name' => 'Gym',
                'order' => 1
            ],
            [
                'name' => 'Tisch Library',
                'informal_name' => 'Tisch Library',
                'order' => 2
            ]
        ];

        foreach($locations as $location) {
            $Location = Location::create($location);

            Monitor::factory()
                ->for($Location)
                ->has(
                    Scan::factory()
                        ->for($Location)
                        ->count(10000)
                )
                ->create();
        }
    }
}
