<x-layouts.app title="Maintenance">
    <div class="flex items-center justify-between mb-6">
        <x-page-heading
            title="Maintenance"
            subtitle="Track repairs and servicing performed on assets."
        />

        <div class="flex gap-2">

            @if (request('mine'))
                <a href="{{ route('maintenance-records.index') }}"
                class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                    All Records
                </a>
            @else
                <a href="{{ route('maintenance-records.index', ['mine' => 1]) }}"
                class="px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                    My Records
                </a>
            @endif

            @can('create', \App\Models\MaintenanceRecord::class)
                <a href="{{ route('maintenance-records.create') }}"
                class="px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-lg hover:bg-slate-800">
                    + New Maintenance Record
                </a>
            @endcan

        </div>
    </div>

    @if (request('mine'))
    <div class="mb-4 flex items-center justify-between bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm text-slate-700">
        <span>
            Showing only maintenance records you've logged ({{ $records->total() }} total)
        </span>

        <a href="{{ route('maintenance-records.index') }}"
            class="text-slate-500 hover:text-slate-900 font-medium">
            Clear
        </a>
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6 p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search issue, technician, or asset..."
                class="flex-1 min-w-[200px] rounded-lg border-gray-300 text-sm">
            <x-filter-select name="status" :options="collect(\App\Enums\MaintenanceStatus::cases())->mapWithKeys(fn($s) => [$s->value => $s->label()])" :selected="request('status')" placeholder="All Statuses" />
            <x-filter-select
                name="created_by"
                :options="$users->pluck('name', 'id')"
                :selected="request('created_by')"
                placeholder="Logged By: Anyone"
            />
            <button class="px-3 py-2 bg-slate-900 text-white rounded-lg text-sm font-medium hover:bg-slate-800">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Issue</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Technician</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cost</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Logged By</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($records as $record)
                        <tr>
                            <td class="px-4 py-3 text-sm">
                                <a href="{{ route('assets.show', $record->asset) }}" class="font-mono text-slate-700 hover:underline">{{ $record->asset->asset_code }}</a>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 max-w-xs truncate">{{ $record->issue }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $record->technician_name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $record->maintenance_date->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $record->cost ? '₱'.number_format($record->cost, 2) : '—' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <x-maintenance-status-badge :status="$record->status" />
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $record->creator->name }}

                                @if ($record->created_by === auth()->id())
                                    <x-badge color="blue">You</x-badge>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-right text-sm">
                            <td class="px-4 py-3 text-right text-sm">
                                @can('update', $record)
                                    <a href="{{ route('maintenance-records.edit', $record) }}" class="text-slate-700 hover:text-slate-900 font-medium">Manage</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><x-empty-state title="No maintenance records found" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">{{ $records->links() }}</div>
    </div>
</x-layouts.app>