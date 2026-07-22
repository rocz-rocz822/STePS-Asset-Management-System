<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('action', 20);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('performed_by_name');
            $table->string('changed_field', 100)->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['asset_id', 'created_at']);
            $table->index('action');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_histories');
    }
};