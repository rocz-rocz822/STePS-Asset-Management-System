<x-layouts.app title="{{ $data['title'] }}">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <x-page-heading :title="$data['title']" />
        <div class="flex gap-2">
            <a href="{{ route('reports.export', [$type->value, 'pdf']) }}?{{ http_build_query(request()->query()) }}"
               class="px-3 py-2 bg-red-50 text-red-700 text-sm font-medium rounded-lg hover:bg-red-100">PDF</a>
            <a href="{{ route('reports.export', [$type->value, 'excel']) }}?{{ http_build_query(request()->query()) }}"
               class="px-3 py-2 bg-green-50 text-green-700 text-sm font-medium rounded-lg hover:bg-green-100">Excel</a>
            <a href="{{ route('reports.export', [$type->value, 'csv']) }}?{{ http_build_query(request()->query()) }}"
               class="px-3 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">CSV</a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6 p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border-gray-300 text-sm">
            </div>
            <button class="px-3 py-2 bg-slate-900 text-white rounded-lg text-sm font-medium hover:bg-slate-800">Apply</button>
            <a href="{{ route('reports.show', $type->value) }}" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200">Reset</a>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        @foreach ($data['headers'] as $header)
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($data['rows'] as $row)
                        <tr>
                            @foreach ($row as $cell)
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($data['headers']) }}"><x-empty-state title="No records found" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 text-sm text-gray-500">
            {{ $data['rows']->count() }} record(s)
        </div>
    </div>
</x-layouts.app>