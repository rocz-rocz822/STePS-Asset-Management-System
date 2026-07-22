<?php

namespace Database\Seeders;

use App\Models\BorrowRecord;
use Illuminate\Database\Seeder;

class BorrowRecordSeeder extends Seeder
{
    public function run(): void
    {
        BorrowRecord::factory()->count(10)->create();
        BorrowRecord::factory()->count(8)->returned()->create();
    }
}