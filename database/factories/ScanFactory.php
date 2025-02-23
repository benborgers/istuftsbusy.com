<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ScanFactory extends Factory
{
    public function definition(): array
    {
        $time = fake()->dateTimeBetween('-1 month', 'now');

        // Seed the faker so it generates multiple of the same MAC address
        fake()->seed(rand(0, $this->count/5));
        $macAddress = fake()->macAddress();
        fake()->seed();

        return [
            'mac_address' => $macAddress,
            'scan_at' => $time
        ];
    }
}
