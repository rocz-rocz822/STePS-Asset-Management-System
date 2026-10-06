<?php

namespace App\Http\Controllers;

use App\Enums\AssetStatus;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $scoped = fn ($query) => $user->isAdmin() ? $query : $query->where('assigned_to', $user->id);

        $stats = [
            'total' => $scoped(Asset::query())->count(),
            'available' => $scoped(Asset::query())->where('status', 'available')->count(),
            'assigned' => $scoped(Asset::query())->where('status', 'assigned')->count(),
            'borrowed' => $scoped(Asset::query())->where('status', 'borrowed')->count(),
            'under_maintenance' => $scoped(Asset::query())->where('status', 'under_maintenance')->count(),
            'under_repair' => $scoped(Asset::query())->where('status', 'under_repair')->count(),
            'disposed' => $scoped(Asset::query())->where('status', 'disposed')->count(),
            'warranty_expiring_soon' => $scoped(Asset::query())
                ->whereNotNull('warranty_expiration')
                ->whereDate('warranty_expiration', '>=', now())
                ->whereDate('warranty_expiration', '<=', now()->addDays(30))
                ->count(),
        ];

        $byCategory = $scoped(
            Asset::query()->join('categories', 'categories.id', '=', 'assets.category_id')
        )->selectRaw('categories.name, count(*) as total')
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->get();

        $byStatus = $scoped(Asset::query())
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get()
            ->map(fn ($row) => ['label' => AssetStatus::from($row->status)->label(), 'total' => $row->total]);

        $byLocation = $scoped(
            Asset::query()->join('locations', 'locations.id', '=', 'assets.location_id')
        )->selectRaw('locations.building, count(*) as total')
            ->groupBy('locations.building')
            ->orderByDesc('total')
            ->get();

        $recentAssets = $scoped(Asset::with(['category', 'location']))
            ->latest()
            ->take(5)
            ->get();

        $recentActivities = Activity::with('causer')
            ->when(! $user->isAdmin(), fn ($q) => $q->where('causer_id', $user->id))
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard', compact('stats', 'byCategory', 'byStatus', 'byLocation', 'recentAssets', 'recentActivities'));
    }
}