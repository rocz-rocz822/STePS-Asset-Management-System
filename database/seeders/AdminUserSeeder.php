<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@steps.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('Steps@1234!A'),
                'role' => 'admin',
                'is_active' => true,
                'is_protected' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}