<?php

namespace App\Http\Controllers;

use App\Models\AssetHistory;
use Illuminate\Http\Request;

class AssignmentLogController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $entries = AssetHistory::query()
            ->where('changed_field', 'assigned_to')
            ->with('asset')
            ->when(
                ! $user->isAdmin(),
                fn ($query) => $query->where(function ($q) use ($user) {
                    $q->where('old_value', (string) $user->id)
                        ->orWhere('new_value', (string) $user->id);
                })
            )
            ->when(
                $request->filled('search'),
                fn ($query) => $query->whereHas('asset', function ($assetQuery) use ($request) {
                    $assetQuery->search($request->input('search'));
                })
            )
            ->when(
                $request->filled('date_from'),
                fn ($query) => $query->whereDate(
                    'created_at',
                    '>=',
                    $request->input('date_from')
                )
            )
            ->when(
                $request->filled('date_to'),
                fn ($query) => $query->whereDate(
                    'created_at',
                    '<=',
                    $request->input('date_to')
                )
            )
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('assignment-log.index', compact('entries'));
    }
}