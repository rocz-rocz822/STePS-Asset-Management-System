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
            ['email' => 'admin@steps.local'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'is_protected' => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'technician@steps.local'],
            [
                'name' => 'Roczpen M. Sto. Domingo',
                'password' => Hash::make('password'),
                'role' => 'technician',
                'is_active' => true,
                'is_protected' => false,
                'email_verified_at' => now(),
            ]
        );
    }
}