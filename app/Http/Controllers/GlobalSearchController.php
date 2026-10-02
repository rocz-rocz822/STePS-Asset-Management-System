<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\BorrowRecord;
use App\Models\MaintenanceRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q'));
        $user = $request->user();

        if (strlen($term) < 2) {
            return response()->json(['results' => []]);
        }

        $results = collect();

        $results = $results->merge(
            Asset::search($term)
                ->when(
                    ! $user->isAdmin(),
                    fn ($q) => $q->where('assigned_to', $user->id)
                )
                ->take(5)
                ->get()
                ->map(fn ($a) => [
                    'type' => 'Asset',
                    'title' => "{$a->asset_code} — {$a->name}",
                    'subtitle' => $a->status->label(),
                    'url' => route('assets.show', $a),
                ])
        );

        $results = $results->merge(
            BorrowRecord::search($term)
                ->with('asset')
                ->when(
                    ! $user->isAdmin(),
                    fn ($q) => $q->whereHas(
                        'asset',
                        fn ($aq) => $aq->where('assigned_to', $user->id)
                    )
                )
                ->take(5)
                ->get()
                ->map(fn ($b) => [
                    'type' => 'Borrow Record',
                    'title' => "{$b->borrower_name} — {$b->asset->asset_code}",
                    'subtitle' => $b->status->label(),
                    'url' => route('borrow-records.index', [
                        'search' => $b->borrower_name,
                    ]),
                ])
        );

        $results = $results->merge(
            MaintenanceRecord::search($term)
                ->with('asset')
                ->when(
                    ! $user->isAdmin(),
                    fn ($q) => $q->whereHas(
                        'asset',
                        fn ($aq) => $aq->where('assigned_to', $user->id)
                    )
                )
                ->take(5)
                ->get()
                ->map(fn ($m) => [
                    'type' => 'Maintenance',
                    'title' => \Illuminate\Support\Str::limit($m->issue, 50),
                    'subtitle' => $m->asset->asset_code,
                    'url' => route('maintenance-records.index', [
                        'search' => $term,
                    ]),
                ])
        );

        if ($user->isAdmin()) {
            $results = $results->merge(
                \App\Models\User::where(
                    'name',
                    'like',
                    "%{$term}%"
                )
                    ->orWhere(
                        'email',
                        'like',
                        "%{$term}%"
                    )
                    ->take(5)
                    ->get()
                    ->map(fn ($u) => [
                        'type' => 'User',
                        'title' => $u->name,
                        'subtitle' => $u->email,
                        'url' => route('users.edit', $u),
                    ])
            );
        }

        return response()->json([
            'results' => $results->take(15)->values(),
        ]);
    }
}