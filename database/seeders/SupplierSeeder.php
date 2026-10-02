<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'UST Purchasing Office',
            'Octagon Computer Superstore',
            'PC Express',
            'Silicon Valley',
            'Thinking Tools',
            'VST ECS',
            'Wordtext Systems',
            'Trends & Technologies',
        ] as $name) {
            Supplier::updateOrCreate([
                'name' => $name,
            ]);
        }
    }
}