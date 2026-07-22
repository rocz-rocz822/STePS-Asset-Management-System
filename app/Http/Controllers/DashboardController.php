<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'total' => Asset::count(),
            'available' => Asset::where('status', 'available')->count(),
            'assigned' => Asset::where('status', 'assigned')->count(),
            'borrowed' => Asset::where('status', 'borrowed')->count(),
            'under_maintenance' => Asset::where('status', 'under_maintenance')->count(),
            'under_repair' => Asset::where('status', 'under_repair')->count(),
            'disposed' => Asset::where('status', 'disposed')->count(),
            'warranty_expiring_soon' => Asset::whereNotNull('warranty_expiration')
                ->whereDate('warranty_expiration', '>=', now())
                ->whereDate('warranty_expiration', '<=', now()->addDays(30))
                ->count(),
        ];

        if (! auth()->user()->isAdmin()) {
            $stats['mine'] = Asset::where('created_by', auth()->id())->count();
        }

        $byCategory = Asset::join('categories', 'categories.id', '=', 'assets.category_id')
            ->selectRaw('categories.name, COUNT(*) as total')
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->get();

        $byStatus = Asset::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->map(function ($row) {
                return [
                    'label' => $row->status->label(),
                    'total' => $row->total,
                ];
            });

        $byLocation = Asset::join('locations', 'locations.id', '=', 'assets.location_id')
            ->selectRaw('locations.building, COUNT(*) as total')
            ->groupBy('locations.building')
            ->orderByDesc('total')
            ->get();

        $recentAssets = Asset::with(['category', 'location'])
            ->latest()
            ->take(5)
            ->get();

        $recentActivities = Activity::with('causer')
            ->when(
                ! auth()->user()->isAdmin(),
                fn ($query) => $query->where('causer_id', auth()->id())
            )
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard', compact(
            'stats',
            'byCategory',
            'byStatus',
            'byLocation',
            'recentAssets',
            'recentActivities'
        ));
    }
}