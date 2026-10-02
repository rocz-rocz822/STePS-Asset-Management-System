<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\AccountDeletionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Unlink the user's Google account.
     */
    public function unlinkGoogle(Request $request): RedirectResponse
    {
        $request->user()->update([
            'google_id' => null,
        ]);

        return back()->with('status', 'google-unlinked');
    }

    /**
     * Request account deletion.
     */
    public function requestDeletion(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->is_protected) {
            abort(403, 'This account is protected and cannot request deletion.');
        }

        $request->validate([
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        AccountDeletionRequest::updateOrCreate(
            [
                'user_id' => $user->id,
                'status' => 'pending',
            ],
            [
                'reason' => $request->reason,
            ]
        );

        return back()->with('status', 'deletion-requested');
    }
}