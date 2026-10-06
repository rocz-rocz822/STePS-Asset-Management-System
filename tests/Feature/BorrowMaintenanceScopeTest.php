<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\BorrowRecord;
use App\Models\MaintenanceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowMaintenanceScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_technician_only_sees_borrow_records_for_their_own_assets(): void
    {
        $technician = User::factory()->create(['role' => 'technician']);
        $myAsset = Asset::factory()->create(['assigned_to' => $technician->id]);
        $otherAsset = Asset::factory()->create(['assigned_to' => null]);

        $myRecord = BorrowRecord::factory()->create(['asset_id' => $myAsset->id]);
        $otherRecord = BorrowRecord::factory()->create(['asset_id' => $otherAsset->id]);

        $response = $this->actingAs($technician)->get(route('borrow-records.index'));

        $response->assertSee($myAsset->asset_code);
        $response->assertDontSee($otherAsset->asset_code);
    }

    public function test_admin_sees_all_borrow_records(): void
    {
        $admin = User::factory()->admin()->create();
        $assetA = Asset::factory()->create();
        $assetB = Asset::factory()->create();

        BorrowRecord::factory()->create(['asset_id' => $assetA->id]);
        BorrowRecord::factory()->create(['asset_id' => $assetB->id]);

        $response = $this->actingAs($admin)->get(route('borrow-records.index'));

        $response->assertSee($assetA->asset_code);
        $response->assertSee($assetB->asset_code);
    }

    public function test_technician_only_sees_maintenance_records_for_their_own_assets(): void
    {
        $technician = User::factory()->create(['role' => 'technician']);
        $myAsset = Asset::factory()->create(['assigned_to' => $technician->id]);
        $otherAsset = Asset::factory()->create(['assigned_to' => null]);

        MaintenanceRecord::factory()->create(['asset_id' => $myAsset->id]);
        MaintenanceRecord::factory()->create(['asset_id' => $otherAsset->id]);

        $response = $this->actingAs($technician)->get(route('maintenance-records.index'));

        $response->assertSee($myAsset->asset_code);
        $response->assertDontSee($otherAsset->asset_code);
    }
}