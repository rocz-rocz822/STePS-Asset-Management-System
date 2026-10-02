<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Convert any existing 'viewer' accounts to 'technician' before changing the column.
        DB::table('users')->where('role', 'viewer')->update(['role' => 'technician']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('technician')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('technician')->change();
        });
    }
};