<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrow_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->restrictOnDelete();

            $table->string('borrower_name', 150);
            $table->string('borrower_department', 150)->nullable();
            $table->string('borrower_contact', 100)->nullable();

            $table->text('purpose')->nullable();
            $table->date('borrow_date');
            $table->date('expected_return_date');
            $table->date('actual_return_date')->nullable();

            $table->string('status', 20)->default('borrowed');
            $table->string('previous_asset_status', 30)->nullable();

            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();

            $table->timestamps();

            $table->index(['asset_id', 'status']);
            $table->index('expected_return_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrow_records');
    }
};