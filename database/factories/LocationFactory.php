<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'building' => $this->faker->randomElement(['Main Building', 'Annex Building', 'Warehouse']),
            'floor' => $this->faker->randomElement(['Ground Floor', '2nd Floor', '3rd Floor']),
            'room' => $this->faker->randomElement(['IT Office', 'Server Room', 'Storage Room', 'Admin Office']),
            'storage_area' => $this->faker->optional()->randomElement(['Cabinet A', 'Cabinet B', 'Shelf 1']),
            'description' => $this->faker->optional()->sentence(),
            'is_active' => true,
        ];
    }
}