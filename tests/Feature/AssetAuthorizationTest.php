<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_technician_can_edit_own_asset(): void
    {
        $technician = User::factory()->create(['role' => 'technician']);
        $asset = Asset::factory()->create(['assigned_to' => $technician->id]);

        $this->actingAs($technician)
            ->get(route('assets.edit', $asset))
            ->assertOk();
    }

    public function test_technician_cannot_edit_others_asset(): void
    {
        $technicianA = User::factory()->create(['role' => 'technician']);
        $technicianB = User::factory()->create(['role' => 'technician']);
        $asset = Asset::factory()->create(['assigned_to' => $technicianA->id]);

        $this->actingAs($technicianB)
            ->get(route('assets.edit', $asset))
            ->assertForbidden();
    }

    public function test_technician_cannot_delete_asset(): void
    {
        $technician = User::factory()->create(['role' => 'technician']);
        $asset = Asset::factory()->create(['assigned_to' => $technician->id]);

        $this->actingAs($technician)
            ->delete(route('assets.destroy', $asset))
            ->assertForbidden();
    }

    public function test_admin_can_edit_any_asset(): void
    {
        $admin = User::factory()->admin()->create();
        $technician = User::factory()->create(['role' => 'technician']);
        $asset = Asset::factory()->create(['assigned_to' => $technician->id]);

        $this->actingAs($admin)
            ->get(route('assets.edit', $asset))
            ->assertOk();
    }

    public function test_technician_cannot_access_trashed_assets(): void
    {
        $technician = User::factory()->create(['role' => 'technician']);

        $this->actingAs($technician)
            ->get(route('assets.trashed'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('assets.index'))->assertRedirect(route('login'));
    }
}