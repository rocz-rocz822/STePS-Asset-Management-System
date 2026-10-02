<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\BorrowRecord;
use App\Models\MaintenanceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffAndOwnershipTest extends TestCase
{
    use RefreshDatabase;

    // --- Staff visibility ---

    public function test_staff_only_sees_assets_assigned_to_them(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $myAsset = Asset::factory()->create(['assigned_to' => $staff->id]);
        $otherAsset = Asset::factory()->create(['assigned_to' => null]);

        $response = $this->actingAs($staff)->get(route('assets.index'));

        $response->assertSee($myAsset->asset_code);
        $response->assertDontSee($otherAsset->asset_code);
    }

    public function test_staff_cannot_view_asset_not_assigned_to_them(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $otherAsset = Asset::factory()->create(['assigned_to' => null]);

        $this->actingAs($staff)
            ->get(route('assets.show', $otherAsset))
            ->assertForbidden();
    }

    public function test_staff_cannot_edit_even_their_own_asset(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $myAsset = Asset::factory()->create(['assigned_to' => $staff->id]);

        $this->actingAs($staff)
            ->get(route('assets.edit', $myAsset))
            ->assertForbidden();
    }

    // --- Technician ownership pivot (assigned_to, not created_by) ---

    public function test_technician_can_edit_asset_assigned_to_them_even_if_created_by_someone_else(): void
    {
        $admin = User::factory()->admin()->create();
        $technician = User::factory()->create(['role' => 'technician', 'can_manage_assets' => true]);

        $asset = Asset::factory()->create([
            'created_by' => $admin->id,
            'assigned_to' => $technician->id,
        ]);

        $this->actingAs($technician)
            ->get(route('assets.edit', $asset))
            ->assertOk();
    }

    public function test_technician_cannot_edit_asset_they_created_but_is_assigned_to_someone_else(): void
    {
        $technician = User::factory()->create(['role' => 'technician', 'can_manage_assets' => true]);
        $otherUser = User::factory()->create(['role' => 'technician']);

        $asset = Asset::factory()->create([
            'created_by' => $technician->id,
            'assigned_to' => $otherUser->id,
        ]);

        $this->actingAs($technician)
            ->get(route('assets.edit', $asset))
            ->assertForbidden();
    }

    // --- Borrowing/Maintenance scoped to assigned assets ---

    public function test_staff_can_log_borrow_for_their_own_assigned_asset(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $asset = Asset::factory()->create(['assigned_to' => $staff->id, 'status' => 'available']);

        $response = $this->actingAs($staff)->post(route('borrow-records.store'), [
            'asset_id' => $asset->id,
            'borrower_name' => 'Test Borrower',
            'borrow_date' => now()->format('Y-m-d'),
            'expected_return_date' => now()->addDays(3)->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('borrow-records.index'));
        $this->assertDatabaseHas('borrow_records', ['asset_id' => $asset->id]);
    }

    public function test_staff_cannot_log_borrow_for_asset_not_assigned_to_them(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $asset = Asset::factory()->create(['assigned_to' => null, 'status' => 'available']);

        $response = $this->actingAs($staff)->post(route('borrow-records.store'), [
            'asset_id' => $asset->id,
            'borrower_name' => 'Test Borrower',
            'borrow_date' => now()->format('Y-m-d'),
            'expected_return_date' => now()->addDays(3)->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors('asset_id');
        $this->assertDatabaseMissing('borrow_records', ['asset_id' => $asset->id]);
    }

    public function test_staff_can_log_maintenance_for_their_own_assigned_asset(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $asset = Asset::factory()->create(['assigned_to' => $staff->id]);

        $response = $this->actingAs($staff)->post(route('maintenance-records.store'), [
            'asset_id' => $asset->id,
            'maintenance_date' => now()->format('Y-m-d'),
            'technician_id' => $staff->id,
            'issue' => 'Test issue',
            'status' => 'pending',
            'resulting_asset_status' => 'under_repair',
        ]);

        $response->assertRedirect(route('maintenance-records.index'));
        $this->assertDatabaseHas('maintenance_records', ['asset_id' => $asset->id]);
    }

    // --- Force Status is Admin-only and requires a reason ---

    public function test_admin_can_force_status_with_a_reason(): void
    {
        $admin = User::factory()->admin()->create();
        $asset = Asset::factory()->create(['status' => 'lost']);

        $response = $this->actingAs($admin)->patch(route('assets.force-status', $asset), [
            'status' => 'available',
            'reason' => 'Asset was recovered and returned by the borrower directly.',
        ]);

        $response->assertRedirect(route('assets.show', $asset));
        $this->assertEquals('available', $asset->fresh()->status->value);
    }

    public function test_force_status_requires_a_reason(): void
    {
        $admin = User::factory()->admin()->create();
        $asset = Asset::factory()->create(['status' => 'lost']);

        $response = $this->actingAs($admin)->patch(route('assets.force-status', $asset), [
            'status' => 'available',
            'reason' => '',
        ]);

        $response->assertSessionHasErrors('reason');
    }

    public function test_technician_cannot_use_force_status(): void
    {
        $technician = User::factory()->create(['role' => 'technician', 'can_manage_assets' => true]);
        $asset = Asset::factory()->create(['status' => 'lost']);

        $this->actingAs($technician)
            ->patch(route('assets.force-status', $asset), [
                'status' => 'available',
                'reason' => 'Trying to bypass.',
            ])
            ->assertForbidden();
    }
}