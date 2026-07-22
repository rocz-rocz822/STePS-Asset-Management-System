<x-layouts.app title="Borrowing">
    <div class="flex items-center justify-between mb-6">
        <x-page-heading
            title="Borrowing"
            subtitle="Track assets checked out to staff."
        />

        <div class="flex gap-2">

            @if (request('mine'))
                <a href="{{ route('borrow-records.index') }}"
                class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                    All Records
                </a>
            @else
                <a href="{{ route('borrow-records.index', ['mine' => 1]) }}"
                class="px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                    My Records
                </a>
            @endif

            @can('create', \App\Models\BorrowRecord::class)
                <a href="{{ route('borrow-records.create') }}"
                class="px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-lg hover:bg-slate-800">
                    + New Borrow Record
                </a>
            @endcan

        </div>
    </div>

    @if (request('mine'))
    <div class="mb-4 flex items-center justify-between bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm text-slate-700">

            <span>
                Showing only borrow records you've logged
                ({{ $records->total() }} total)
            </span>

            <a href="{{ route('borrow-records.index') }}"
            class="text-slate-500 hover:text-slate-900 font-medium">
                Clear
            </a>

        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6 p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search borrower or asset..."
                class="flex-1 min-w-[200px] rounded-lg border-gray-300 text-sm">
            <x-filter-select name="status" :options="collect(\App\Enums\BorrowStatus::cases())->mapWithKeys(fn($s) => [$s->value => $s->label()])" :selected="request('status')" placeholder="All Statuses" />
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Borrower</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Borrow Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expected Return</th>
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
                                <span class="block text-xs text-gray-400">{{ $record->asset->name }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                {{ $record->borrower_name }}
                                @if ($record->borrower_department)
                                    <span class="block text-xs text-gray-400">{{ $record->borrower_department }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $record->borrow_date->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $record->expected_return_date->format('M d, Y') }}
                                @if ($record->isOverdue())
                                    <x-badge color="red">Overdue</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm"><x-borrow-status-badge :status="$record->status" /></td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $record->creator?->name ?? 'Unknown' }}

                                @if ($record->created_by === auth()->id())
                                    <x-badge color="blue">You</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm">
                                @can('update', $record)
                                    <a href="{{ route('borrow-records.edit', $record) }}" class="text-slate-700 hover:text-slate-900 font-medium">Manage</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><x-empty-state title="No borrow records found" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">{{ $records->links() }}</div>
    </div>
</x-layouts.app>