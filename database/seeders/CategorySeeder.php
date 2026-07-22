<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Desktop', 'Laptop', 'Server', 'Monitor', 'Printer', 'Scanner',
            'UPS', 'Router', 'Switch', 'Firewall', 'Access Point', 'NAS',
            'Storage', 'Projector', 'Telephone', 'CCTV', 'Tablet',
            'Peripherals', 'Computer Parts', 'IT Tools', 'Office Equipment',
            'Furniture', 'Others',
        ];

        foreach ($categories as $name) {
            Category::updateOrCreate(['name' => $name]);
        }
    }
}