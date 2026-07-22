<?php

namespace App\Http\Controllers;

use App\Enums\BorrowStatus;
use App\Http\Requests\StoreBorrowRequest;
use App\Http\Requests\UpdateBorrowRequest;
use App\Models\Asset;
use App\Models\BorrowRecord;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BorrowRecordController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', BorrowRecord::class);

        $records = BorrowRecord::query()
            ->with(['asset', 'creator'])
            ->search($request->search)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('asset_id'), fn ($q) => $q->where('asset_id', $request->asset_id))
            ->when($request->filled('created_by'), fn ($q) => $q->where('created_by', $request->created_by))
            ->when($request->boolean('mine'), fn ($q) => $q->where('created_by', auth()->id()))
            ->latest('borrow_date')
            ->paginate(15)
            ->withQueryString();

        $users = User::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('borrow-records.index', compact(
            'records',
            'users'
        ));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', BorrowRecord::class);

        $assets = Asset::where('status', 'available')->orderBy('name')->get();
        $preselectedAssetId = $request->integer('asset_id') ?: null;

        return view('borrow-records.create', compact('assets', 'preselectedAssetId'));
    }

    public function store(StoreBorrowRequest $request): RedirectResponse
    {
        $asset = Asset::findOrFail($request->asset_id);

        abort_if($asset->status !== \App\Enums\AssetStatus::Available, 422, 'This asset is not currently available to borrow.');

        DB::transaction(function () use ($request, $asset) {
            BorrowRecord::create([
                ...$request->validated(),
                'status' => BorrowStatus::Borrowed->value,
                'previous_asset_status' => $asset->status->value,
                'created_by' => auth()->id(),
            ]);

            $asset->update(['status' => 'borrowed', 'updated_by' => auth()->id()]);
        });

        return redirect()->route('borrow-records.index')->with('success', 'Borrow record created and asset marked as borrowed.');
    }

    public function edit(BorrowRecord $borrowRecord): View
    {
        $this->authorize('update', $borrowRecord);

        return view('borrow-records.edit', ['record' => $borrowRecord]);
    }

    public function update(UpdateBorrowRequest $request, BorrowRecord $borrowRecord): RedirectResponse
    {
        DB::transaction(function () use ($request, $borrowRecord) {
            $borrowRecord->update($request->validated());

            // Only revert the asset's status if this borrow is being closed out.
            if (in_array($request->status, ['returned', 'lost'])) {
                $borrowRecord->asset->update([
                    'status' => $request->status === 'lost' ? 'lost' : ($borrowRecord->previous_asset_status ?? 'available'),
                    'updated_by' => auth()->id(),
                ]);
            }
        });

        return redirect()->route('borrow-records.index')->with('success', 'Borrow record updated successfully.');
    }
}