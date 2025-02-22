<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ScanFactory extends Factory
{
    public function definition(): array
    {
        $time = fake()->dateTimeBetween('-1 month', 'now');

        return [
            'mac_address' => fake()->macAddress(),
            'updated_at' => $time,
            'created_at' => $time
        ];
    }
}
