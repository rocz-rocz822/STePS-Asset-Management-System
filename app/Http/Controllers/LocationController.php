<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Location::class);

        $locations = Location::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('building', 'like', "%{$request->search}%")
                        ->orWhere('room', 'like', "%{$request->search}%")
                        ->orWhere('storage_area', 'like', "%{$request->search}%");
                });
            })
            ->orderBy('building')
            ->paginate(15)
            ->withQueryString();

        return view('locations.index', compact('locations'));
    }

    public function create(): View
    {
        $this->authorize('create', Location::class);

        return view('locations.create');
    }

    public function store(StoreLocationRequest $request): RedirectResponse
    {
        Location::create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', false),
        ]);

        return redirect()->route('locations.index')->with('success', 'Location created successfully.');
    }

    public function edit(Location $location): View
    {
        $this->authorize('update', $location);

        return view('locations.edit', compact('location'));
    }

    public function update(UpdateLocationRequest $request, Location $location): RedirectResponse
    {
        $location->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', false),
        ]);

        return redirect()->route('locations.index')->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        $this->authorize('delete', $location);

        // Phase 3 will add: abort_if($location->assets()->exists(), 403, 'Cannot delete a location with assets assigned.');
        $location->delete();

        return redirect()->route('locations.index')->with('success', 'Location deleted successfully.');
    }
}