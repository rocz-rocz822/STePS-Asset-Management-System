<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Activity::query()->with('causer')->latest();

        // Technicians can only ever see their own actions.
        if (! $request->user()->isAdmin()) {
            $query->where('causer_type', User::class)->where('causer_id', $request->user()->id);
        } elseif ($request->filled('user_id')) {
            $query->where('causer_type', User::class)->where('causer_id', $request->user_id);
        }

        $activities = $query
            ->when($request->filled('log_name'), fn ($q) => $q->where('log_name', $request->log_name))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->paginate(20)
            ->withQueryString();

        $users = $request->user()->isAdmin() ? User::orderBy('name')->get() : collect();

        return view('activity-logs.index', compact('activities', 'users'));
    }
}