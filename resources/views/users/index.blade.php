<x-layouts.app title="User Management">
    <x-page-heading title="User Management" subtitle="Manage IT department staff accounts." />

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
            <form method="GET" class="flex gap-2 flex-1 max-w-md">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search name or email..."
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-slate-500 focus:ring-slate-500"
                >

                <select name="role" class="rounded-lg border-gray-300 text-sm">
                    <option value="">All Roles</option>
                    <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                    <option value="technician" @selected(request('role') === 'technician')>Technician</option>
                </select>

                <button class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-medium hover:bg-gray-200">
                    Filter
                </button>
            </form>

            <a
                href="{{ route('users.create') }}"
                class="inline-flex items-center justify-center px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-lg hover:bg-slate-800"
            >
                + Add User
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Name
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Email
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Role
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Asset Access
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Status
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                {{ $user->name }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $user->email }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-500 capitalize">
                                {{ $user->role }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                @if ($user->isAdmin())
                                    <x-badge color="gray">Full (Admin)</x-badge>
                                @elseif ($user->can_manage_assets)
                                    <x-badge color="green">Add/Edit</x-badge>
                                @else
                                    <x-badge color="yellow">View Only</x-badge>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm">
                                @if ($user->is_active)
                                    <x-badge color="green">Active</x-badge>
                                @else
                                    <x-badge color="red">Inactive</x-badge>
                                @endif
                            </td>

<td class="px-4 py-3 text-right text-sm space-x-3">

    @if ($user->is_protected && $user->id !== auth()->id())

        <span class="text-gray-400 text-xs">
            No actions available
        </span>

    @else

        <a
            href="{{ route('users.edit', $user) }}"
            class="text-slate-700 hover:text-slate-900 font-medium"
        >
            Edit
        </a>


        @if ($user->is_protected)

            <x-badge color="gray">
                Protected
            </x-badge>

        @else

            @can('toggleStatus', $user)

                <form
                    method="POST"
                    action="{{ route('users.toggle-status', $user) }}"
                    class="inline"
                >
                    @csrf
                    @method('PATCH')

                    <button class="text-yellow-700 hover:text-yellow-900 font-medium">
                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                    </button>

                </form>

            @endcan


            @can('delete', $user)

                <button
                    type="button"
                    @click="$dispatch('open-modal-delete-user-{{ $user->id }}')"
                    class="text-red-600 hover:text-red-800 font-medium"
                >
                    Delete
                </button>


                <x-confirm-modal
                    id="delete-user-{{ $user->id }}"
                    title="Delete this user account?"
                    message="{{ $user->name }} will lose access immediately. This cannot be undone."
                    :action="route('users.destroy', $user)"
                />

            @endcan

        @endif

    @endif

</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state title="No users found" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
    </div>
</x-layouts.app>