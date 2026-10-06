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

    public function test_admin_can_approve_a_deletion_request(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create([
            'role' => 'technician',
            'is_active' => true,
        ]);

        $this
            ->actingAs($user)
            ->post('/profile/request-deletion', [
                'reason' => 'Leaving the department',
            ]);

        $deletionRequest = \App\Models\AccountDeletionRequest::where(
            'user_id',
            $user->id
        )->first();

        $response = $this
            ->actingAs($admin)
            ->post(
                route('account-deletion-requests.approve', $deletionRequest)
            );

        $response->assertRedirect();

        $this->assertFalse($user->fresh()->is_active);
        $this->assertEquals(
            'approved',
            $deletionRequest->fresh()->status
        );

        // Confirm the account still exists — never deleted.
        $this->assertNotNull($user->fresh());
    }

    public function test_admin_can_deny_a_deletion_request(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create([
            'role' => 'technician',
            'is_active' => true,
        ]);

        $this
            ->actingAs($user)
            ->post('/profile/request-deletion', [
                'reason' => 'Changed my mind later',
            ]);

        $deletionRequest = \App\Models\AccountDeletionRequest::where(
            'user_id',
            $user->id
        )->first();

        $response = $this
            ->actingAs($admin)
            ->post(
                route('account-deletion-requests.deny', $deletionRequest),
                [
                    'review_note' => 'Please discuss with your supervisor first.',
                ]
            );

        $response->assertRedirect();

        $this->assertTrue($user->fresh()->is_active);
        $this->assertEquals(
            'denied',
            $deletionRequest->fresh()->status
        );
    }

    public function test_admin_cannot_approve_deletion_for_a_protected_account(): void
    {
        $admin = User::factory()->admin()->create();

        $protectedAdmin = User::factory()->admin()->create([
            'is_protected' => true,
        ]);

        $deletionRequest = \App\Models\AccountDeletionRequest::create([
            'user_id' => $protectedAdmin->id,
            'status' => 'pending',
        ]);

        $this
            ->actingAs($admin)
            ->post(
                route('account-deletion-requests.approve', $deletionRequest)
            )
            ->assertForbidden();

        $this->assertTrue($protectedAdmin->fresh()->is_active);
    }
}