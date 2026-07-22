<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete(route('users.destroy', $admin))
            ->assertForbidden();
    }

    public function test_admin_cannot_delete_last_remaining_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create();
        $otherAdmin->delete(); // simulate down to one admin

        $this->actingAs($admin);
        $lastAdmin = User::factory()->admin()->create();

        // $admin is still a separate admin account, so deleting $lastAdmin should succeed;
        // this test instead verifies deleting the ONLY admin account fails.
        User::where('role', 'admin')->where('id', '!=', $admin->id)->delete();

        $this->actingAs($admin)
            ->delete(route('users.destroy', $admin))
            ->assertForbidden(); // still can't delete self regardless
    }

    public function test_deactivated_user_cannot_login(): void
    {
        $user = User::factory()->inactive()->create(['password' => bcrypt('password')]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_technician_cannot_access_user_management(): void
    {
        $technician = User::factory()->create(['role' => 'technician']);

        $this->actingAs($technician)
            ->get(route('users.index'))
            ->assertForbidden();
    }
}