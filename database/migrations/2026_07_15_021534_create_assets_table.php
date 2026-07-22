<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code', 50)->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();

            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('location_id')->constrained()->restrictOnDelete();

            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('serial_number', 150)->nullable()->unique();
            $table->string('property_number', 100)->nullable();
            $table->string('inventory_number', 100)->nullable();
            $table->string('manufacturer', 150)->nullable();
            $table->string('supplier', 150)->nullable();

            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 12, 2)->nullable();
            $table->date('warranty_expiration')->nullable();

            $table->string('status', 30)->default('available');
            $table->string('condition', 30)->default('good');

            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();

            $table->text('remarks')->nullable();
            $table->string('photo_path')->nullable();

            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('condition');
            $table->index('warranty_expiration');
            $table->index(['category_id', 'location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};