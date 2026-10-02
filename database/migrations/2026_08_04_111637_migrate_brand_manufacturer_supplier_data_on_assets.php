<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: add the new foreign key columns.
        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('category_id')->constrained('brands')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->after('brand_id')->constrained('suppliers')->nullOnDelete();
        });

        // Step 2: backfill Brand records from existing distinct brand/manufacturer text values.
        $existingBrands = DB::table('assets')
            ->whereNotNull('brand')
            ->distinct()
            ->pluck('brand')
            ->merge(
                DB::table('assets')->whereNotNull('manufacturer')->distinct()->pluck('manufacturer')
            )
            ->map(fn ($name) => trim($name))
            ->filter()
            ->unique()
            ->values();

        foreach ($existingBrands as $brandName) {
            DB::table('brands')->insertOrIgnore([
                'name' => $brandName,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Step 3: backfill Supplier records from existing distinct supplier text values.
        $existingSuppliers = DB::table('assets')
            ->whereNotNull('supplier')
            ->distinct()
            ->pluck('supplier')
            ->map(fn ($name) => trim($name))
            ->filter()
            ->unique()
            ->values();

        foreach ($existingSuppliers as $supplierName) {
            DB::table('suppliers')->insertOrIgnore([
                'name' => $supplierName,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Step 4: link every existing asset to its matching Brand/Supplier record.
        // Prefer the 'brand' text value; fall back to 'manufacturer' if brand was empty.
        DB::table('assets')->orderBy('id')->chunk(100, function ($assets) {
            foreach ($assets as $asset) {
                $brandName = trim($asset->brand ?: $asset->manufacturer ?: '');
                $supplierName = trim($asset->supplier ?: '');

                $updates = [];

                if ($brandName !== '') {
                    $brand = DB::table('brands')->where('name', $brandName)->first();
                    if ($brand) {
                        $updates['brand_id'] = $brand->id;
                    }
                }

                if ($supplierName !== '') {
                    $supplier = DB::table('suppliers')->where('name', $supplierName)->first();
                    if ($supplier) {
                        $updates['supplier_id'] = $supplier->id;
                    }
                }

                if (! empty($updates)) {
                    DB::table('assets')->where('id', $asset->id)->update($updates);
                }
            }
        });

        // Step 5: drop the old free-text columns now that data is migrated.
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['brand', 'manufacturer', 'supplier']);
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('brand', 100)->nullable();
            $table->string('manufacturer', 150)->nullable();
            $table->string('supplier', 150)->nullable();
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('brand_id');
            $table->dropConstrainedForeignId('supplier_id');
        });
    }
};