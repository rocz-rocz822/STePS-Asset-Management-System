<?php

namespace Database\Factories;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        $purchaseDate = $this->faker->dateTimeBetween('-3 years', '-1 month');

        $creatorId = User::query()->inRandomOrder()->value('id') ?? User::factory();

        return [

            'asset_code' => 'STEPS-' . now()->year . '-' . $this->faker->unique()->numerify('#####'),

            'name' => $this->faker->randomElement([
                'Dell OptiPlex 7010',
                'HP LaserJet Pro',
                'Lenovo ThinkPad T14',
                'Cisco Catalyst 2960',
                'APC Smart-UPS 1500',
            ]),

            'description' => $this->faker->optional()->sentence(),

            'category_id' => Category::query()->inRandomOrder()->value('id') ?? Category::factory(),

            'location_id' => Location::query()->inRandomOrder()->value('id') ?? Location::factory(),

            'brand' => $this->faker->randomElement([
                'Dell',
                'HP',
                'Lenovo',
                'Cisco',
                'APC',
            ]),

            'model' => $this->faker->bothify('Model-####'),

            'serial_number' => strtoupper(
                $this->faker->unique()->bothify('SN-????-########')
            ),

            'property_number' => $this->faker->optional()->numerify('PN-######'),

            'inventory_number' => $this->faker->optional()->numerify('INV-######'),

            'manufacturer' => $this->faker->company(),

            'supplier' => $this->faker->company(),

            'purchase_date' => $purchaseDate,

            'purchase_cost' => $this->faker->randomFloat(2, 500, 80000),

            'warranty_expiration' => $this->faker->dateTimeBetween($purchaseDate, '+3 years'),

            'status' => $this->faker->randomElement(AssetStatus::cases())->value,

            'condition' => $this->faker->randomElement(AssetCondition::cases())->value,

            'assigned_to' => null,

            'remarks' => $this->faker->optional()->sentence(),

            'photo_path' => null,

            'created_by' => $creatorId,

            'updated_by' => $creatorId,
        ];
    }
}