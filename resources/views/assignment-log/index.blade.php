<x-layouts.app title="Assignment Log">

    <x-page-heading
        title="Assignment Log"
        :subtitle="auth()->user()->isAdmin()
            ? 'Every asset assignment in the system.'
            : 'A record of your asset assignments.'"
    />

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6 p-4">

        <form method="GET" class="flex flex-wrap gap-3 items-end">

            {{-- Search --}}
            <div class="flex-1 min-w-[200px]">
                <label
                    for="search"
                    class="block text-xs text-gray-400 mb-1"
                >
                    Search
                </label>

                <input
                    id="search"
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search asset code or name..."
                    class="block w-full rounded-lg border-gray-300 text-sm focus:border-slate-500 focus:ring-slate-500"
                >
            </div>

            {{-- From --}}
            <div>
                <label
                    for="date_from"
                    class="block text-xs text-gray-400 mb-1"
                >
                    From
                </label>

                <input
                    id="date_from"
                    type="date"
                    name="date_from"
                    value="{{ request('date_from') }}"
                    class="rounded-lg border-gray-300 text-sm focus:border-slate-500 focus:ring-slate-500"
                >
            </div>

            {{-- To --}}
            <div>
                <label
                    for="date_to"
                    class="block text-xs text-gray-400 mb-1"
                >
                    To
                </label>

                <input
                    id="date_to"
                    type="date"
                    name="date_to"
                    value="{{ request('date_to') }}"
                    class="rounded-lg border-gray-300 text-sm focus:border-slate-500 focus:ring-slate-500"
                >
            </div>

            {{-- Filter --}}
            <button
                type="submit"
                class="px-4 py-2 bg-slate-900 text-white rounded-lg text-sm font-medium hover:bg-slate-800"
            >
                Filter
            </button>

            {{-- Reset --}}
            <a
                href="{{ route('assignment-log.index') }}"
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200"
            >
                Reset
            </a>

        </form>

    </div>

    {{-- Assignment Log Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-100">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Date
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Asset
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Assigned From
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Assigned To
                        </th>

                        @if (auth()->user()->isAdmin())
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Assigned By
                            </th>
                        @endif

                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Note
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse ($entries as $entry)

                        @php
                            $asset = $entry->asset;
                        @endphp

                        <tr class="hover:bg-gray-50">

                            {{-- Date --}}
                            <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                                {{ $entry->created_at->format('M d, Y g:ia') }}
                            </td>

                            {{-- Asset --}}
                            <td class="px-4 py-3 text-sm">

                                @if (
                                    $asset &&
                                    ! $asset->trashed() &&
                                    auth()->user()->can('view', $asset)
                                )

                                    <a
                                        href="{{ route('assets.show', $asset) }}"
                                        class="font-mono text-slate-700 hover:underline"
                                    >
                                        {{ $asset->asset_code }}
                                    </a>

                                @else

                                    <span class="font-mono text-gray-700">
                                        {{ $asset->asset_code ?? '—' }}
                                    </span>

                                @endif

                                @if ($asset)
                                    <span class="block text-xs text-gray-400">
                                        {{ $asset->name }}
                                    </span>
                                @endif

                            </td>

                            {{-- Assigned From --}}
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $entry->display_old_value }}
                            </td>

                            {{-- Assigned To --}}
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                {{ $entry->display_new_value }}
                            </td>

                            {{-- Assigned By --}}
                            @if (auth()->user()->isAdmin())
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $entry->performed_by_name ?? 'System' }}
                                </td>
                            @endif

                            {{-- Note --}}
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $entry->remarks ?? '—' }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="{{ auth()->user()->isAdmin() ? 6 : 5 }}"
                                class="px-4 py-8"
                            >
                                <x-empty-state
                                    title="No assignments recorded yet"
                                />
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- Pagination --}}
        @if ($entries->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $entries->links() }}
            </div>
        @endif

    </div>

</x-layouts.app>