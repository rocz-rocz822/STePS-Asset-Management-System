<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_request_account_deletion(): void
    {
        $user = User::factory()->create(['role' => 'technician']);

        $response = $this
            ->actingAs($user)
            ->post('/profile/request-deletion', [
                'reason' => 'No longer needed',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('account_deletion_requests', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        // Account itself is untouched — still exists and still active.
        $this->assertNotNull($user->fresh());
        $this->assertTrue($user->fresh()->is_active);
    }

    public function test_protected_account_cannot_request_deletion(): void
    {
        $admin = User::factory()->admin()->create(['is_protected' => true]);

        $this
            ->actingAs($admin)
            ->post('/profile/request-deletion')
            ->assertForbidden();
    }
}