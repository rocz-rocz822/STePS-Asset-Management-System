<x-layouts.app title="{{ $asset->asset_code }}">
    <div class="flex items-start justify-between mb-6">
        <x-page-heading :title="$asset->name" :subtitle="$asset->asset_code" />

        <div class="flex gap-2">
            @can('update', $asset)
                <a
                    href="{{ route('assets.edit', $asset) }}"
                    class="px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50"
                >
                    Edit
                </a>
            @endcan

            @can('delete', $asset)
                <button
                    type="button"
                    @click="$dispatch('open-modal-delete-asset')"
                    class="px-4 py-2 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100"
                >
                    Delete
                </button>

                <x-confirm-modal
                    id="delete-asset"
                    title="Delete this asset?"
                    message="{{ $asset->asset_code }} will be moved to trash and can be restored by an administrator."
                    :action="route('assets.destroy', $asset)"
                />
            @endcan

            @can('forceStatus', $asset)
                <button
                    type="button"
                    @click="$dispatch('open-modal-force-status')"
                    class="px-4 py-2 bg-yellow-50 text-yellow-700 text-sm font-medium rounded-lg hover:bg-yellow-100"
                >
                    Force Status
                </button>

                <div
                    x-data="{ open: false }"
                    x-on:open-modal-force-status.window="open = true"
                    x-show="open"
                    x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center px-4"
                >
                    <div
                        class="fixed inset-0 bg-black/40"
                        @click="open = false"
                    ></div>

                    <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Force Change Status
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Use this only for genuine exceptions — e.g. recovering a Lost asset,
                            or correcting a mistaken status. This bypasses the normal
                            Borrowing/Maintenance automation and is permanently logged.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('assets.force-status', $asset) }}"
                            class="mt-4 space-y-4"
                        >
                            @csrf
                            @method('PATCH')

                            <div>
                                <x-input-label
                                    for="force_status"
                                    value="New Status"
                                />

                                <select
                                    id="force_status"
                                    name="status"
                                    class="mt-1 block w-full rounded-lg border-gray-300 text-sm"
                                    required
                                >
                                    @foreach (\App\Enums\AssetStatus::cases() as $status)
                                        <option
                                            value="{{ $status->value }}"
                                            @selected($asset->status->value === $status->value)
                                        >
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label
                                    for="reason"
                                    value="Reason (required)"
                                />

                                <textarea
                                    id="reason"
                                    name="reason"
                                    rows="3"
                                    class="mt-1 block w-full rounded-lg border-gray-300 text-sm"
                                    required
                                    placeholder="e.g. Asset recovered and returned by borrower directly"
                                ></textarea>
                            </div>

                            <div class="flex justify-end gap-3 pt-2">
                                <button
                                    type="button"
                                    @click="open = false"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200"
                                >
                                    Cancel
                                </button>

                                <button
                                    type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-lg hover:bg-yellow-700"
                                >
                                    Confirm Override
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">
                    Overview
                </h3>

                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Category</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $asset->category->name }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Location</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $asset->location->full_name }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Status</dt>
                        <dd>
                            <x-status-badge :status="$asset->status" />
                        </dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Condition</dt>
                        <dd>
                            <x-condition-badge :condition="$asset->condition" />
                        </dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Assigned To</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $asset->assignedUser?->name ?? 'Unassigned' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Description</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $asset->description ?? '—' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">
                    Identification
                </h3>

                <dl class="grid grid-cols-2 gap-4 text-sm">

                    <div>
                        <dt class="text-gray-500">Brand / Manufacturer</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $asset->brand->name ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Model</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $asset->model ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Serial Number</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $asset->serial_number ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Property Number</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $asset->property_number ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Supplier</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $asset->supplier->name ?? '—' }}
                        </dd>
                    </div>

                </dl>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">
                    Purchase & Warranty
                </h3>

                <dl class="grid grid-cols-2 gap-4 text-sm">

                    <div>
                        <dt class="text-gray-500">Purchase Date</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $asset->purchase_date?->format('M d, Y') ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Purchase Cost</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $asset->purchase_cost ? '₱'.number_format($asset->purchase_cost, 2) : '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Warranty Expiration</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $asset->warranty_expiration?->format('M d, Y') ?? '—' }}

                            @if ($asset->warrantyExpiringSoon())
                                <x-badge color="yellow">
                                    Expiring Soon
                                </x-badge>
                            @elseif ($asset->warranty_expiration && ! $asset->isUnderWarranty())
                                <x-badge color="red">
                                    Expired
                                </x-badge>
                            @endif
                        </dd>
                    </div>

                </dl>
            </div>

            @if ($asset->remarks)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-2">
                        Remarks
                    </h3>

                    <p class="text-sm text-gray-600">
                        {{ $asset->remarks }}
                    </p>
                </div>
            @endif

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">
                    Attachments
                </h3>

                @forelse ($asset->attachments as $attachment)
                    <div class="flex items-center justify-between text-sm bg-gray-50 rounded-lg px-3 py-2 mb-2">
                        <div>
                            <a
                                href="{{ $attachment->url }}"
                                target="_blank"
                                class="text-slate-700 hover:underline font-medium"
                            >
                                {{ $attachment->original_name }}
                            </a>

                            <span class="text-xs text-gray-400 block">
                                {{ $attachment->human_size }} · uploaded by {{ $attachment->uploader->name }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">
                        No attachments uploaded.
                    </p>
                @endforelse
            </div>

            @if (auth()->user()->isAdmin() || $asset->assigned_to === auth()->id())
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">

                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-900">
                            History
                        </h3>

                        <a
                            href="{{ route('asset-histories.index', ['asset_id' => $asset->id]) }}"
                            class="text-xs text-slate-600 hover:underline"
                        >
                            View full history
                        </a>
                    </div>

                    <div class="space-y-3">

                        @forelse ($asset->histories as $history)

                            <div class="flex items-start gap-3 text-sm border-b border-gray-50 pb-3 last:border-0 last:pb-0">

                                <x-badge :color="$history->action->color()">
                                    {{ $history->action->label() }}
                                </x-badge>

                                <div class="flex-1">

                                    @if ($history->changed_field)

                                        <p class="text-gray-700">
                                            <span class="font-medium">
                                                {{ $history->field_label }}
                                            </span>

                                            changed from

                                            <span class="text-gray-500">
                                                {{ $history->display_old_value }}
                                            </span>

                                            to

                                            <span class="text-gray-900 font-medium">
                                                {{ $history->display_new_value }}
                                            </span>
                                        </p>

                                    @else

                                        <p class="text-gray-700">
                                            {{ $history->remarks }}
                                        </p>

                                    @endif

                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $history->performed_by_name }}
                                        ·
                                        {{ $history->created_at->format('M d, Y g:ia') }}
                                    </p>

                                </div>
                            </div>

                        @empty

                            <p class="text-sm text-gray-400">
                                No history recorded yet.
                            </p>

                        @endforelse

                    </div>
                </div>
            @endif

        </div>

        <div class="space-y-6">

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 text-center">

                @if ($asset->photo_path)
                    <img
                        src="{{ Storage::url($asset->photo_path) }}"
                        class="w-full h-48 object-cover rounded-lg border border-gray-100"
                    >
                @else
                    <div class="w-full h-48 flex items-center justify-center bg-gray-50 rounded-lg text-gray-400 text-sm">
                        No photo
                    </div>
                @endif

            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">

                <h3 class="text-sm font-semibold text-gray-900 mb-3">
                    Record Info
                </h3>

                <dl class="text-sm space-y-2">

                    <div class="flex justify-between">
                        <dt class="text-gray-500">Created By</dt>
                        <dd class="text-gray-900">
                            {{ $asset->creator->name }}
                        </dd>
                    </div>

                    <div class="flex justify-between">
                        <dt class="text-gray-500">Created At</dt>
                        <dd class="text-gray-900">
                            {{ $asset->created_at->format('M d, Y g:ia') }}
                        </dd>
                    </div>

                    @if ($asset->updater)

                        <div class="flex justify-between">
                            <dt class="text-gray-500">Last Updated By</dt>
                            <dd class="text-gray-900">
                                {{ $asset->updater->name }}
                            </dd>
                        </div>

                        <div class="flex justify-between">
                            <dt class="text-gray-500">Last Updated At</dt>
                            <dd class="text-gray-900">
                                {{ $asset->updated_at->format('M d, Y g:ia') }}
                            </dd>
                        </div>

                    @endif

                </dl>
            </div>

            @can('viewAny', \App\Models\BorrowRecord::class)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">

                    <div class="flex items-center justify-between mb-4">

                        <h3 class="text-sm font-semibold text-gray-900">
                            Borrowing History
                        </h3>

                        @can('create', \App\Models\BorrowRecord::class)
                            <a
                                href="{{ route('borrow-records.create', ['asset_id' => $asset->id]) }}"
                                class="text-xs text-slate-600 hover:underline"
                            >
                                + Log Borrow
                            </a>
                        @endcan

                    </div>

                    @forelse ($asset->borrowRecords as $borrow)

                        <div class="flex items-center justify-between text-sm border-b border-gray-50 py-2 last:border-0">

                            <div>
                                <span class="font-medium text-gray-900">
                                    {{ $borrow->borrower_name }}
                                </span>

                                <span class="text-gray-400">
                                    · {{ $borrow->borrow_date->format('M d, Y') }}
                                </span>
                            </div>

                            <x-borrow-status-badge :status="$borrow->status" />

                        </div>

                    @empty

                        <p class="text-sm text-gray-400">
                            No borrowing history.
                        </p>

                    @endforelse

                </div>
            @endcan

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">

                <div class="flex items-center justify-between mb-4">

                    <h3 class="text-sm font-semibold text-gray-900">
                        Maintenance History
                    </h3>

                    @can('create', \App\Models\MaintenanceRecord::class)
                        <a
                            href="{{ route('maintenance-records.create', ['asset_id' => $asset->id]) }}"
                            class="text-xs text-slate-600 hover:underline"
                        >
                            + Log Maintenance
                        </a>
                    @endcan

                </div>

                @forelse ($asset->maintenanceRecords as $maint)

                    <div class="flex items-center justify-between text-sm border-b border-gray-50 py-2 last:border-0">

                        <div>
                            <span class="font-medium text-gray-900 truncate">
                                {{ \Illuminate\Support\Str::limit($maint->issue, 40) }}
                            </span>

                            <span class="text-gray-400">
                                · {{ $maint->maintenance_date->format('M d, Y') }}
                            </span>
                        </div>

                        <x-maintenance-status-badge :status="$maint->status" />

                    </div>

                @empty

                    <p class="text-sm text-gray-400">
                        No maintenance history.
                    </p>

                @endforelse

            </div>

        </div>

    </div>

</x-layouts.app>