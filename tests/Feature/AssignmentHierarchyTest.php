<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentHierarchyTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test Monitor',
            'category_id' => Category::factory()->create()->id,
            'location_id' => Location::factory()->create()->id,
            'status' => 'available',
            'condition' => 'good',
        ], $overrides);
    }

    public function test_staff_can_open_the_add_asset_page(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $this->actingAs($staff)
            ->get(route('assets.create'))
            ->assertOk();
    }

    public function test_staff_asset_is_assigned_to_themselves(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $this->actingAs($staff)
            ->post(
                route('assets.store'),
                $this->payload()
            );

        $this->assertDatabaseHas('assets', [
            'name' => 'Test Monitor',
            'assigned_to' => $staff->id,
        ]);
    }

    public function test_staff_cannot_assign_to_someone_else(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $other = User::factory()->create([
            'role' => 'staff',
        ]);

        $response = $this->actingAs($staff)
            ->post(
                route('assets.store'),
                $this->payload([
                    'assigned_to' => $other->id,
                ])
            );

        $response->assertSessionHasErrors('assigned_to');

        $this->assertDatabaseMissing('assets', [
            'name' => 'Test Monitor',
        ]);
    }

    public function test_technician_can_assign_to_staff(): void
    {
        $tech = User::factory()->create([
            'role' => 'technician',
            'can_manage_assets' => true,
        ]);

        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $this->actingAs($tech)
            ->post(
                route('assets.store'),
                $this->payload([
                    'assigned_to' => $staff->id,
                ])
            );

        $this->assertDatabaseHas('assets', [
            'name' => 'Test Monitor',
            'assigned_to' => $staff->id,
        ]);

        $this->assertDatabaseHas('asset_histories', [
            'changed_field' => 'assigned_to',
            'new_value' => (string) $staff->id,
            'user_id' => $tech->id,
        ]);
    }

    public function test_technician_can_assign_to_another_technician(): void
    {
        $techA = User::factory()->create([
            'role' => 'technician',
            'can_manage_assets' => true,
        ]);

        $techB = User::factory()->create([
            'role' => 'technician',
            'can_manage_assets' => true,
        ]);

        $this->actingAs($techA)
            ->post(
                route('assets.store'),
                $this->payload([
                    'assigned_to' => $techB->id,
                ])
            );

        $this->assertDatabaseHas('assets', [
            'name' => 'Test Monitor',
            'assigned_to' => $techB->id,
        ]);
    }

    public function test_technician_can_assign_to_themselves(): void
    {
        $tech = User::factory()->create([
            'role' => 'technician',
            'can_manage_assets' => true,
        ]);

        $this->actingAs($tech)
            ->post(
                route('assets.store'),
                $this->payload([
                    'assigned_to' => $tech->id,
                ])
            );

        $this->assertDatabaseHas('assets', [
            'name' => 'Test Monitor',
            'assigned_to' => $tech->id,
        ]);
    }

    public function test_technician_cannot_assign_to_admin(): void
    {
        $tech = User::factory()->create([
            'role' => 'technician',
            'can_manage_assets' => true,
        ]);

        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($tech)
            ->post(
                route('assets.store'),
                $this->payload([
                    'assigned_to' => $admin->id,
                ])
            );

        $response->assertSessionHasErrors('assigned_to');

        $this->assertDatabaseMissing('assets', [
            'name' => 'Test Monitor',
        ]);
    }

    public function test_technician_must_pick_someone_to_assign_to(): void
    {
        $tech = User::factory()->create([
            'role' => 'technician',
            'can_manage_assets' => true,
        ]);

        $this->actingAs($tech)
            ->post(
                route('assets.store'),
                $this->payload()
            )
            ->assertSessionHasErrors('assigned_to');
    }

    public function test_assignment_log_only_shows_the_users_own_assignments(): void
    {
        $techA = User::factory()->create([
            'role' => 'technician',
        ]);

        $techB = User::factory()->create([
            'role' => 'technician',
        ]);

        // The asset is assigned to Tech A.
        //
        // The AssignmentLogController filters non-admin users
        // using old_value/new_value, so Tech A should see it
        // while Tech B should not.
        $asset = Asset::factory()->create([
            'assigned_to' => $techA->id,
        ]);

        AssetHistory::create([
            'asset_id' => $asset->id,
            'action' => 'created',
            'user_id' => $techA->id,
            'performed_by_name' => $techA->name,
            'changed_field' => 'assigned_to',
            'old_value' => null,
            'new_value' => (string) $techA->id,
        ]);

        // Tech A can see their own assignment.
        $this->actingAs($techA)
            ->get(route('assignment-log.index'))
            ->assertOk()
            ->assertSee($asset->asset_code);

        // Tech B cannot see Tech A's assignment.
        $this->actingAs($techB)
            ->get(route('assignment-log.index'))
            ->assertOk()
            ->assertDontSee($asset->asset_code);
    }
}