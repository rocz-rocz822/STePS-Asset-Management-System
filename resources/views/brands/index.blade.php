<x-layouts.app title="Brand Management">
    <x-page-heading title="Brands" subtitle="Manage the brand/manufacturer list used across the system." />

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
            <form method="GET" class="flex gap-2 flex-1 max-w-md">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search brands..."
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-slate-500 focus:ring-slate-500">
                <button class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-medium hover:bg-gray-200">Search</button>
            </form>

            <a href="{{ route('brands.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-lg hover:bg-slate-800">
                + Add Brand
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($brands as $brand)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $brand->name }}</td>
                            <td class="px-4 py-3 text-sm">
                                <x-badge :color="$brand->is_active ? 'green' : 'red'">
                                    {{ $brand->is_active ? 'Active' : 'Inactive' }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-3 text-right text-sm space-x-3">
                                <a href="{{ route('brands.edit', $brand) }}" class="text-slate-700 hover:text-slate-900 font-medium">Edit</a>
                                <button type="button" @click="$dispatch('open-modal-delete-brand-{{ $brand->id }}')" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                                <x-confirm-modal
                                    id="delete-brand-{{ $brand->id }}"
                                    title="Delete this brand?"
                                    message="'{{ $brand->name }}' will be permanently removed."
                                    :action="route('brands.destroy', $brand)"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3"><x-empty-state title="No brands found" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $brands->links() }}
        </div>
    </div>
</x-layouts.app>