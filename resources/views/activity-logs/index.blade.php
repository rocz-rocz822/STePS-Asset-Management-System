<x-layouts.app title="Activity Logs">
    <x-page-heading title="Activity Logs" subtitle="{{ auth()->user()->isAdmin() ? 'System-wide user activity.' : 'Your account activity.' }}" />

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6 p-4">
        <form method="GET" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @if (auth()->user()->isAdmin())
                <x-filter-select name="user_id" :options="$users->pluck('name', 'id')" :selected="request('user_id')" placeholder="All Users" />
            @endif
            <x-filter-select name="log_name" :options="['asset' => 'Asset', 'auth' => 'Authentication']" :selected="request('log_name')" placeholder="All Types" />
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border-gray-300 text-sm" title="From">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border-gray-300 text-sm" title="To">
            <button class="px-3 py-2 bg-slate-900 text-white rounded-lg text-sm font-medium hover:bg-slate-800">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        @if (auth()->user()->isAdmin())
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        @endif
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Browser</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($activities as $activity)
                        <tr>
                            @if (auth()->user()->isAdmin())
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $activity->causer?->name ?? 'System' }}</td>
                            @endif
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $activity->description }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $activity->properties['ip_address'] ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 max-w-xs truncate" title="{{ $activity->properties['browser'] ?? '' }}">
                                {{ \Illuminate\Support\Str::limit($activity->properties['browser'] ?? '—', 40) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $activity->created_at->format('M d, Y g:ia') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-empty-state title="No activity recorded yet" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $activities->links() }}
        </div>
    </div>
</x-layouts.app>