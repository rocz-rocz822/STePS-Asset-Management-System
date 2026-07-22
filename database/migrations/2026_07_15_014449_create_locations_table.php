<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('building', 100);
            $table->string('floor', 50)->nullable();
            $table->string('room', 100)->nullable();
            $table->string('storage_area', 100)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
            $table->index(['building', 'floor', 'room']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};