<x-layouts.app title="Account Deletion Requests">
    <x-page-heading title="Account Deletion Requests" subtitle="Review requests from users who want their account deactivated." />

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requested</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($requests as $req)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $req->user->name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $req->reason ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $req->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-sm">
                                <x-badge :color="match($req->status) { 'pending' => 'yellow', 'approved' => 'red', 'denied' => 'gray' }">
                                    {{ ucfirst($req->status) }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-3 text-right text-sm space-x-3">
                                @if ($req->status === 'pending')
                                    <form method="POST" action="{{ route('account-deletion-requests.approve', $req) }}" class="inline" onsubmit="return confirm('Deactivate {{ $req->user->name }}\'s account? Their assets and history will remain untouched.');">
                                        @csrf
                                        <button class="text-red-600 hover:text-red-800 font-medium">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('account-deletion-requests.deny', $req) }}" class="inline">
                                        @csrf
                                        <button class="text-gray-600 hover:text-gray-900 font-medium">Deny</button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">Reviewed by {{ $req->reviewer->name ?? '—' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-empty-state title="No deletion requests" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">{{ $requests->links() }}</div>
    </div>
</x-layouts.app>