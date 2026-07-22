<x-layouts.app title="Asset History">
    <x-page-heading title="Asset History" subtitle="Complete audit trail of every change made to assets." />

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6 p-4">
        <form method="GET" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <x-filter-select name="asset_id" :options="$assets->pluck('asset_code', 'id')" :selected="request('asset_id')" placeholder="All Assets" />
            <x-filter-select name="action" :options="collect(\App\Enums\AssetHistoryAction::cases())->mapWithKeys(fn($a) => [$a->value => $a->label()])" :selected="request('action')" placeholder="All Actions" />
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border-gray-300 text-sm" title="From">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border-gray-300 text-sm" title="To">
            <button class="px-3 py-2 bg-slate-900 text-white rounded-lg text-sm font-medium hover:bg-slate-800 col-span-2 sm:col-span-1">Filter</button>
            <a href="{{ route('asset-histories.index') }}" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 text-center col-span-2 sm:col-span-1">Reset</a>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Change</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">By</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">When</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($histories as $history)
                        <tr>
                            <td class="px-4 py-3 text-sm">
                                <a href="{{ route('assets.show', $history->asset_id) }}" class="font-mono text-slate-700 hover:underline">{{ $history->asset->asset_code ?? '—' }}</a>
                            </td>
                            <td class="px-4 py-3 text-sm"><x-badge :color="$history->action->color()">{{ $history->action->label() }}</x-badge></td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                @if ($history->changed_field)
                                    <span class="font-medium">{{ $history->field_label }}</span>:
                                    {{ $history->display_old_value }} → {{ $history->display_new_value }}
                                @else
                                    {{ $history->remarks }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $history->performed_by_name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $history->created_at->format('M d, Y g:ia') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-empty-state title="No history records found" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $histories->links() }}
        </div>
    </div>
</x-layouts.app>