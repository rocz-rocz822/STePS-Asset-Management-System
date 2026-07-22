<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaintenanceRequest;
use App\Http\Requests\UpdateMaintenanceRequest;
use App\Models\Asset;
use App\Models\MaintenanceRecord;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MaintenanceRecordController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', MaintenanceRecord::class);

        $records = MaintenanceRecord::query()
            ->with(['asset', 'technician', 'creator'])
            ->search($request->search)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('asset_id'), fn ($q) => $q->where('asset_id', $request->asset_id))
            ->when($request->filled('created_by'), fn ($q) => $q->where('created_by', $request->created_by))
            ->when($request->boolean('mine'), fn ($q) => $q->where('created_by', auth()->id()))
            ->latest('maintenance_date')
            ->paginate(15)
            ->withQueryString();

        $users = User::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('maintenance-records.index', compact(
            'records',
            'users'
        ));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', MaintenanceRecord::class);

        $assets = Asset::orderBy('name')->get();
        $technicians = User::orderBy('name')->get();
        $preselectedAssetId = $request->integer('asset_id') ?: null;

        return view('maintenance-records.create', compact('assets', 'technicians', 'preselectedAssetId'));
    }

    public function store(StoreMaintenanceRequest $request): RedirectResponse
    {
        $asset = Asset::findOrFail($request->asset_id);
        $technician = User::findOrFail($request->technician_id);

        DB::transaction(function () use ($request, $asset, $technician) {
            $record = MaintenanceRecord::create([
                ...$request->safe()->except('attachments'),
                'technician_name' => $technician->name,
                'previous_asset_status' => $asset->status->value,
                'created_by' => auth()->id(),
            ]);

            if (in_array($request->status, ['pending', 'in_progress']) && $request->resulting_asset_status) {
                $asset->update(['status' => $request->resulting_asset_status, 'updated_by' => auth()->id()]);
            }

            $this->storeAttachments($request, $record);
        });

        return redirect()->route('maintenance-records.index')->with('success', 'Maintenance record created successfully.');
    }

    public function edit(MaintenanceRecord $maintenanceRecord): View
    {
        $this->authorize('update', $maintenanceRecord);

        $technicians = User::orderBy('name')->get();

        return view('maintenance-records.edit', ['record' => $maintenanceRecord, 'technicians' => $technicians]);
    }

    public function update(UpdateMaintenanceRequest $request, MaintenanceRecord $maintenanceRecord): RedirectResponse
    {
        $technician = User::findOrFail($request->technician_id);

        DB::transaction(function () use ($request, $maintenanceRecord, $technician) {
            $maintenanceRecord->update([
                ...$request->safe()->except('attachments'),
                'technician_name' => $technician->name,
            ]);

            if (in_array($request->status, ['pending', 'in_progress']) && $request->resulting_asset_status) {
                $maintenanceRecord->asset->update(['status' => $request->resulting_asset_status, 'updated_by' => auth()->id()]);
            } elseif (in_array($request->status, ['completed', 'cancelled'])) {
                $maintenanceRecord->asset->update([
                    'status' => $maintenanceRecord->previous_asset_status ?? 'available',
                    'updated_by' => auth()->id(),
                ]);
            }

            $this->storeAttachments($request, $maintenanceRecord);
        });

        return redirect()->route('maintenance-records.index')->with('success', 'Maintenance record updated successfully.');
    }

    private function storeAttachments(Request $request, MaintenanceRecord $record): void
    {
        if (! $request->hasFile('attachments')) {
            return;
        }

        foreach ($request->file('attachments') as $file) {
            $path = $file->store('maintenance/attachments', 'public');

            $record->attachments()->create([
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
        }
    }
}