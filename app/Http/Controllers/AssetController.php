<?php

namespace App\Http\Controllers;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Asset::class);

        $assets = Asset::query()
            ->with(['category', 'location', 'assignedUser', 'creator'])
            ->search($request->search)
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('location_id'), fn ($q) => $q->where('location_id', $request->location_id))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('condition'), fn ($q) => $q->where('condition', $request->condition))
            ->when($request->filled('assigned_to'), fn ($q) => $q->where('assigned_to', $request->assigned_to))
            ->when($request->filled('created_by'), fn ($q) => $q->where('created_by', $request->created_by))
            ->when($request->boolean('mine'), fn ($q) => $q->where('created_by', auth()->id()))
            ->when($request->filled('purchase_from'), fn ($q) => $q->whereDate('purchase_date', '>=', $request->purchase_from))
            ->when($request->filled('purchase_to'), fn ($q) => $q->whereDate('purchase_date', '<=', $request->purchase_to))
            ->when($request->filled('warranty') && $request->warranty === 'expiring_soon', function ($q) {
                $q->whereNotNull('warranty_expiration')
                    ->whereDate('warranty_expiration', '>=', now())
                    ->whereDate('warranty_expiration', '<=', now()->addDays(30));
            })
            ->when($request->filled('warranty') && $request->warranty === 'expired', function ($q) {
                $q->whereNotNull('warranty_expiration')
                    ->whereDate('warranty_expiration', '<', now());
            })
            ->when(
                $request->filled('sort') &&
                in_array($request->sort, [
                    'name',
                    'asset_code',
                    'purchase_date',
                    'status',
                    'created_at',
                ]),
                fn ($q) => $q->orderBy(
                    $request->sort,
                    $request->get('direction', 'asc')
                ),
                fn ($q) => $q->latest()
            )
            ->paginate(15)
            ->withQueryString();

        $categories = Category::active()->orderBy('name')->get();
        $locations = Location::active()->orderBy('building')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('assets.index', compact(
            'assets',
            'categories',
            'locations',
            'users'
        ));
    }

    public function create(): View
    {
        $this->authorize('create', Asset::class);

        return view('assets.create', $this->formData());
    }

    public function store(StoreAssetRequest $request): RedirectResponse
    {
        $asset = DB::transaction(function () use ($request) {

            $asset = Asset::create([
                ...$request->safe()->except([
                    'photo',
                    'attachments',
                ]),
                'created_by' => auth()->id(),
            ]);

            if ($request->hasFile('photo')) {
                $asset->update([
                    'photo_path' => $request->file('photo')
                        ->store('assets/photos', 'public'),
                ]);
            }

            $this->storeAttachments($request, $asset);

            return $asset;
        });

        return redirect()
            ->route('assets.index')
            ->with(
                'success',
                "Asset {$asset->asset_code} created successfully."
            );
    }

    public function show(Asset $asset): View
    {
        $this->authorize('view', $asset);

        $asset->load([
            'category',
            'location',
            'assignedUser',
            'creator',
            'updater',
            'attachments.uploader',

            'histories' => fn ($query) => $query
                ->latest()
                ->take(5),

            'borrowRecords' => fn ($query) => $query
                ->latest('borrow_date')
                ->take(5),

            'maintenanceRecords' => fn ($query) => $query
                ->latest('maintenance_date')
                ->take(5),
        ]);

        return view('assets.show', compact('asset'));
    }

    public function edit(Asset $asset): View
    {
        $this->authorize('update', $asset);

        return view('assets.edit', [
            ...$this->formData(),
            'asset' => $asset,
        ]);
    }

    public function update(UpdateAssetRequest $request, Asset $asset): RedirectResponse
    {
        DB::transaction(function () use ($request, $asset) {

            $asset->update([
                ...$request->safe()->except([
                    'photo',
                    'attachments',
                ]),
                'updated_by' => auth()->id(),
            ]);

            if ($request->hasFile('photo')) {

                if ($asset->photo_path) {
                    Storage::disk('public')->delete($asset->photo_path);
                }

                $asset->update([
                    'photo_path' => $request->file('photo')
                        ->store('assets/photos', 'public'),
                ]);
            }

            $this->storeAttachments($request, $asset);
        });

        return redirect()
            ->route('assets.show', $asset)
            ->with(
                'success',
                "Asset {$asset->asset_code} updated successfully."
            );
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        $this->authorize('delete', $asset);

        $asset->delete();

        return redirect()
            ->route('assets.index')
            ->with(
                'success',
                "Asset {$asset->asset_code} deleted successfully."
            );
    }

    public function trashed(Request $request): View
    {
        $this->authorize('viewAny', Asset::class);
        $this->authorize('delete', new Asset());

        $assets = Asset::onlyTrashed()
            ->with([
                'category',
                'location',
            ])
            ->search($request->search)
            ->latest('deleted_at')
            ->paginate(15)
            ->withQueryString();

        return view('assets.trashed', compact('assets'));
    }

    public function restore(int $assetId): RedirectResponse
    {
        $asset = Asset::onlyTrashed()->findOrFail($assetId);

        $this->authorize('restore', $asset);

        $asset->restore();

        return redirect()
            ->route('assets.trashed')
            ->with(
                'success',
                "Asset {$asset->asset_code} restored successfully."
            );
    }

    public function deleteAttachment(
        Asset $asset,
        \App\Models\Attachment $attachment
    ): RedirectResponse {
        $this->authorize('manageAttachments', $asset);

        abort_unless(
            $attachment->attachable_id === $asset->id,
            404
        );

        Storage::disk('public')->delete($attachment->file_path);

        $attachment->delete();

        return back()->with(
            'success',
            'Attachment removed.'
        );
    }

    private function storeAttachments(
        Request $request,
        Asset $asset
    ): void {
        if (! $request->hasFile('attachments')) {
            return;
        }

        foreach ($request->file('attachments') as $file) {

            $path = $file->store(
                'assets/attachments',
                'public'
            );

            $asset->attachments()->create([
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
        }
    }

    private function formData(): array
    {
        return [
            'categories' => Category::active()
                ->orderBy('name')
                ->get(),

            'locations' => Location::active()
                ->orderBy('building')
                ->get(),

            'users' => User::where('is_active', true)
                ->orderBy('name')
                ->get(),

            'statuses' => AssetStatus::cases(),

            'conditions' => AssetCondition::cases(),
        ];
    }
}