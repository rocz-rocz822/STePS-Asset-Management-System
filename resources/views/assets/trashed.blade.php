<x-layouts.app title="Deleted Assets">
    <x-page-heading title="Deleted Assets" subtitle="Assets removed from active inventory. Restore if deleted in error." />

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset Code</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deleted At</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($assets as $asset)
                        <tr>
                            <td class="px-4 py-3 text-sm font-mono text-gray-700">{{ $asset->asset_code }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $asset->name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $asset->deleted_at->format('M d, Y g:ia') }}</td>
                            <td class="px-4 py-3 text-right text-sm">
                                <form method="POST" action="{{ route('assets.restore', $asset->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button class="text-green-700 hover:text-green-900 font-medium">Restore</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><x-empty-state title="No deleted assets" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $assets->links() }}
        </div>
    </div>
</x-layouts.app>