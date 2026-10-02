<x-layouts.app title="Assets">
    <div class="flex items-center justify-between mb-6">
        <x-page-heading
            title="Assets"
            subtitle="Browse and manage all IT department assets."
        />

        <div class="flex gap-2">

            @can('viewTrashed', \App\Models\Asset::class)
                <a
                    href="{{ route('assets.trashed') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200"
                >
                    Trash
                </a>
            @endcan

            @can('create', \App\Models\Asset::class)
                <a
                    href="{{ route('assets.create') }}"
                    class="px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-lg hover:bg-slate-800"
                >
                    + Add Asset
                </a>
            @endcan

        </div>
    </div>

    {{-- Admin-only All Assets / My Assets toggle --}}
    @if (auth()->user()->isAdmin())
        @if (request('mine'))
            <div class="mb-4 flex items-center justify-between rounded-lg bg-blue-50 px-4 py-3 text-sm text-blue-700">
                <span>
                    Showing only assets assigned to you ({{ $assets->total() }} total)
                </span>

                <a
                    href="{{ route('assets.index') }}"
                    class="font-medium text-blue-800 hover:text-blue-900"
                >
                    View All Assets
                </a>
            </div>
        @else
            <div class="mb-4 flex items-center justify-between rounded-lg bg-slate-50 px-4 py-3 text-sm text-slate-700">
                <span>
                    Showing all assets ({{ $assets->total() }} total)
                </span>

                <a
                    href="{{ route('assets.index', ['mine' => 1]) }}"
                    class="font-medium text-slate-800 hover:text-slate-900"
                >
                    View My Assets
                </a>
            </div>
        @endif
    @endif

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6 p-5">
        <form method="GET" class="space-y-4">

            <div>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search assets..."
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-slate-500 focus:ring-slate-500"
                >
            </div>

            <div class="flex flex-wrap gap-3 items-end">

                <x-filter-select
                    name="category_id"
                    :options="$categories->pluck('name', 'id')"
                    :selected="request('category_id')"
                    placeholder="All Categories"
                />

                <x-filter-select
                    name="location_id"
                    :options="$locations->pluck('full_name', 'id')"
                    :selected="request('location_id')"
                    placeholder="All Locations"
                />

                <x-filter-select
                    name="status"
                    :options="collect(\App\Enums\AssetStatus::cases())->mapWithKeys(fn($s) => [$s->value => $s->label()])"
                    :selected="request('status')"
                    placeholder="All Statuses"
                />

                <x-filter-select
                    name="condition"
                    :options="collect(\App\Enums\AssetCondition::cases())->mapWithKeys(fn($c) => [$c->value => $c->label()])"
                    :selected="request('condition')"
                    placeholder="All Conditions"
                />

                <div>
                    <label class="block text-xs text-gray-400 mb-1">
                        Purchased From
                    </label>

                    <input
                        type="date"
                        name="purchase_from"
                        value="{{ request('purchase_from') }}"
                        class="rounded-lg border-gray-300 text-sm"
                    >
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1">
                        Purchased To
                    </label>

                    <input
                        type="date"
                        name="purchase_to"
                        value="{{ request('purchase_to') }}"
                        class="rounded-lg border-gray-300 text-sm"
                    >
                </div>

            </div>

            <div class="flex gap-2">

                <button
                    class="px-4 py-2 bg-slate-900 text-white rounded-lg text-sm font-medium hover:bg-slate-800"
                >
                    Apply Filters
                </button>

                <a
                    href="{{ route('assets.index') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200"
                >
                    Reset
                </a>

            </div>

        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-100">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Asset Code
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Name
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Category
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Location
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Added By
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Status
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Condition
                        </th>

                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse ($assets as $asset)

                        <tr>

                            <td class="px-4 py-3 text-sm font-mono">
                                {{ $asset->asset_code }}
                            </td>

                            <td class="px-4 py-3 text-sm font-medium">
                                {{ $asset->name }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                {{ $asset->category->name }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                {{ $asset->location->full_name }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-500">

                                {{ $asset->creator->name }}

                                @if ($asset->created_by === auth()->id())
                                    <x-badge color="blue">
                                        You
                                    </x-badge>
                                @endif

                            </td>

                            <td class="px-4 py-3">
                                <x-status-badge :status="$asset->status" />
                            </td>

                            <td class="px-4 py-3">
                                <x-condition-badge :condition="$asset->condition" />
                            </td>

                            <td class="px-4 py-3 text-right">

                                <a
                                    href="{{ route('assets.show', $asset) }}"
                                    class="text-slate-700 hover:text-slate-900 font-medium"
                                >
                                    View
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8">

                                <x-empty-state
                                    title="No assets found"
                                    subtitle="Try adjusting your search or filters."
                                />

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $assets->links() }}
        </div>

    </div>

</x-layouts.app>