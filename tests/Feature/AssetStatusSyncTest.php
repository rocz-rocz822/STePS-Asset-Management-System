<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\BorrowRecord;
use App\Models\MaintenanceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetStatusSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_borrowing_an_asset_marks_it_borrowed(): void
    {
        $user = User::factory()->create();
        $asset = Asset::factory()->create(['status' => 'available']);

        $this->actingAs($user)->post(route('borrow-records.store'), [
            'asset_id' => $asset->id,
            'borrower_name' => 'Juan Dela Cruz',
            'borrow_date' => now()->format('Y-m-d'),
            'expected_return_date' => now()->addDays(7)->format('Y-m-d'),
        ]);

        $this->assertEquals('borrowed', $asset->fresh()->status->value);
    }

    public function test_returning_an_asset_reverts_status(): void
    {
        $user = User::factory()->create();
        $asset = Asset::factory()->create(['status' => 'borrowed']);
        $borrow = BorrowRecord::factory()->create([
            'asset_id' => $asset->id,
            'previous_asset_status' => 'available',
            'status' => 'borrowed',
        ]);

        $this->actingAs($user)->put(route('borrow-records.update', $borrow), [
            'status' => 'returned',
            'actual_return_date' => now()->format('Y-m-d'),
        ]);

        $this->assertEquals('available', $asset->fresh()->status->value);
    }

    public function test_completing_maintenance_reverts_asset_status(): void
    {
        $user = User::factory()->create();
        $asset = Asset::factory()->create(['status' => 'under_repair']);
        $maintenance = MaintenanceRecord::factory()->create([
            'asset_id' => $asset->id,
            'technician_id' => $user->id,
            'previous_asset_status' => 'available',
            'status' => 'in_progress',
        ]);

        $this->actingAs($user)->put(route('maintenance-records.update', $maintenance), [
            'maintenance_date' => now()->format('Y-m-d'),
            'technician_id' => $user->id,
            'issue' => 'Test issue',
            'status' => 'completed',
        ]);

        $this->assertEquals('available', $asset->fresh()->status->value);
    }
}