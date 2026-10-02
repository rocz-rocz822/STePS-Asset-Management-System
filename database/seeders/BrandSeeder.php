<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Dell', 'HP', 'Lenovo', 'Cisco', 'APC', 'Asus', 'Acer', 'Brother', 'Canon', 'Epson', 'Samsung', 'LG', 'Ubiquiti', 'TP-Link'] as $name) {
            Brand::updateOrCreate(['name' => $name]);
        }
    }
}