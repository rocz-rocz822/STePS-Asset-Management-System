<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BorrowRecordFactory extends Factory
{
    public function definition(): array
    {
        $borrowDate = $this->faker->dateTimeBetween('-2 months', 'now');

        return [
            'asset_id' => Asset::inRandomOrder()->value('id') ?? Asset::factory(),
            'borrower_name' => $this->faker->name(),
            'borrower_department' => $this->faker->randomElement(['Finance', 'HR', 'Operations', 'Marketing']),
            'borrower_contact' => $this->faker->phoneNumber(),
            'purpose' => $this->faker->sentence(),
            'borrow_date' => $borrowDate,
            'expected_return_date' => (clone $borrowDate)->modify('+7 days'),
            'status' => 'borrowed',
            'created_by' => User::inRandomOrder()->value('id') ?? User::factory(),
        ];
    }

    public function returned(): static
    {
        return $this->state(fn (array $attrs) => [
            'status' => 'returned',
            'actual_return_date' => $this->faker->dateTimeBetween($attrs['borrow_date'] ?? '-1 month', 'now'),
        ]);
    }
}