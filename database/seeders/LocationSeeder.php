<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['building' => 'Main Building', 'floor' => 'Ground Floor', 'room' => 'IT Department Office', 'storage_area' => null],
            ['building' => 'Main Building', 'floor' => 'Ground Floor', 'room' => 'Server Room', 'storage_area' => 'Rack 1'],
            ['building' => 'Main Building', 'floor' => '2nd Floor', 'room' => 'Storage Room', 'storage_area' => 'Cabinet A'],
        ];

        foreach ($locations as $location) {
            Location::updateOrCreate($location);
        }
    }
}