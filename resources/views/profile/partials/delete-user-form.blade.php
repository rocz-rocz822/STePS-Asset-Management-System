@php
    $pending = auth()->user()->accountDeletionRequests()->where('status', 'pending')->first();
@endphp

@if ($pending)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-3">
        <p class="text-sm text-yellow-800 font-medium">Deletion request pending</p>
        <p class="text-xs text-yellow-700 mt-1">
            You requested account deletion on {{ $pending->created_at->format('M d, Y') }}. An administrator will review it. Your account remains fully active until then.
        </p>
    </div>
@else
    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal-request-deletion')"
        class="px-4 py-2 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100"
    >
        Request Account Deletion
    </button>

    <div
        x-data="{ open: false }"
        x-on:open-modal-request-deletion.window="open = true"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
    >
        <div class="fixed inset-0 bg-black/40" @click="open = false"></div>

        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md p-6">
            <h3 class="text-lg font-semibold text-gray-900">
                Request Account Deletion
            </h3>

            <p class="mt-2 text-sm text-gray-500">
                This submits a request to your administrator. Your account won't be removed immediately — once approved, it will be deactivated, not deleted. Any assets, history, or records you've created will remain in the system.
            </p>

            <form method="post" action="{{ route('profile.request-deletion') }}" class="mt-4 space-y-4">
                @csrf

                <div>
                    <x-input-label for="reason" value="Reason (optional)" class="sr-only" />

                    <textarea
                        id="reason"
                        name="reason"
                        rows="3"
                        placeholder="Why are you requesting this? (optional)"
                        class="block w-full rounded-lg border-gray-300 text-sm"
                    ></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button
                        type="button"
                        @click="open = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700"
                    >
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif