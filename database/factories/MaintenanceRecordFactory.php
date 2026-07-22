<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceRecordFactory extends Factory
{
    public function definition(): array
    {
        $technician = User::where('role', 'technician')->inRandomOrder()->first() ?? User::factory()->create();

        return [
            'asset_id' => Asset::inRandomOrder()->value('id') ?? Asset::factory(),
            'maintenance_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'technician_id' => $technician->id,
            'technician_name' => $technician->name,
            'issue' => $this->faker->randomElement(['Not powering on', 'Overheating', 'Blue screen errors', 'Network connectivity issues', 'Slow performance']),
            'resolution' => $this->faker->optional()->sentence(),
            'cost' => $this->faker->optional()->randomFloat(2, 200, 5000),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed']),
            'created_by' => $technician->id,
        ];
    }
}