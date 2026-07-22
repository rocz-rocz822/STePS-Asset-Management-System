@props(['id', 'title' => 'Are you sure?', 'message' => 'This action cannot be undone.', 'action', 'method' => 'DELETE'])

<div
    x-data="{ open: false }"
    x-on:open-modal-{{ $id }}.window="open = true"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center px-4"
>
    <div class="fixed inset-0 bg-black/40" @click="open = false"></div>

    <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
        <p class="mt-2 text-sm text-gray-500">{{ $message }}</p>

        <div class="mt-6 flex justify-end gap-3">
            <button type="button" @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                Cancel
            </button>
            <form method="POST" action="{{ $action }}">
                @csrf
                @if (strtoupper($method) !== 'POST')
                    @method($method)
                @endif
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                    Confirm
                </button>
            </form>
        </div>
    </div>
</div>