<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetHistory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', AssetHistory::class);

        $query = AssetHistory::query()->with(['asset', 'user']);

        // Technicians only ever see history for assets they created.
        if (! $request->user()->isAdmin()) {
            $query->whereHas('asset', fn ($q) => $q->where('created_by', $request->user()->id));
        }

        $histories = $query
            ->when($request->filled('asset_id'), fn ($q) => $q->where('asset_id', $request->asset_id))
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->action))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        // Asset dropdown for the filter — scoped the same way as the results.
        $assets = Asset::query()
            ->when(! $request->user()->isAdmin(), fn ($q) => $q->where('created_by', $request->user()->id))
            ->orderBy('name')
            ->get(['id', 'asset_code', 'name']);

        return view('asset-histories.index', compact('histories', 'assets'));
    }
}