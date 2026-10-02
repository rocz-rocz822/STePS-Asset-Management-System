<?php

namespace App\Http\Controllers;

use App\Models\AccountDeletionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccountDeletionRequestController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', \App\Models\User::class);

        $requests = AccountDeletionRequest::with(['user', 'reviewer'])
            ->orderByRaw("status = 'pending' desc")
            ->latest()
            ->paginate(15);

        return view('account-deletion-requests.index', compact('requests'));
    }

    public function approve(AccountDeletionRequest $accountDeletionRequest): RedirectResponse
    {
        $this->authorize('viewAny', \App\Models\User::class);

        if ($accountDeletionRequest->user->is_protected) {
            abort(403, 'This account is protected and cannot be deactivated.');
        }

        $accountDeletionRequest->user->update(['is_active' => false]);

        $accountDeletionRequest->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Account deactivated. All of their records remain in the system.');
    }

    public function deny(Request $request, AccountDeletionRequest $accountDeletionRequest): RedirectResponse
    {
        $this->authorize('viewAny', \App\Models\User::class);

        $accountDeletionRequest->update([
            'status' => 'denied',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_note' => $request->input('review_note'),
        ]);

        return back()->with('success', 'Deletion request denied. The account remains active.');
    }
}