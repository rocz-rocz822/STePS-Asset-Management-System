<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->restrictOnDelete();

            $table->date('maintenance_date');
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('technician_name');

            $table->text('issue');
            $table->text('resolution')->nullable();
            $table->decimal('cost', 10, 2)->nullable();

            $table->string('status', 20)->default('pending');
            $table->string('resulting_asset_status', 30)->nullable();
            $table->string('previous_asset_status', 30)->nullable();

            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();

            $table->timestamps();

            $table->index(['asset_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};