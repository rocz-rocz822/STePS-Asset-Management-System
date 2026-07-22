<x-layouts.app title="Manage Borrow Record">
    <x-page-heading title="Manage Borrow Record" :subtitle="$record->asset->asset_code . ' — ' . $record->borrower_name" />

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 max-w-2xl">
        <dl class="grid grid-cols-2 gap-4 text-sm mb-6 pb-6 border-b border-gray-100">
            <div><dt class="text-gray-500">Asset</dt><dd class="font-medium text-gray-900">{{ $record->asset->name }}</dd></div>
            <div><dt class="text-gray-500">Borrower</dt><dd class="font-medium text-gray-900">{{ $record->borrower_name }}</dd></div>
            <div><dt class="text-gray-500">Borrow Date</dt><dd class="font-medium text-gray-900">{{ $record->borrow_date->format('M d, Y') }}</dd></div>
            <div><dt class="text-gray-500">Expected Return</dt><dd class="font-medium text-gray-900">{{ $record->expected_return_date->format('M d, Y') }}</dd></div>
        </dl>

        <form method="POST" action="{{ route('borrow-records.update', $record) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="status" value="Status" />
                <select id="status" name="status" class="mt-1 block w-full rounded-lg border-gray-300 text-sm" required>
                    @foreach (\App\Enums\BorrowStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(old('status', $record->status->value) === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="actual_return_date" value="Actual Return Date" />
                <x-text-input id="actual_return_date" name="actual_return_date" type="date" class="mt-1 block w-full" :value="old('actual_return_date', optional($record->actual_return_date)->format('Y-m-d'))" />
                <x-input-error :messages="$errors->get('actual_return_date')" class="mt-2" />
                <p class="text-xs text-gray-400 mt-1">Required when marking as Returned.</p>
            </div>

            <div>
                <x-input-label for="remarks" value="Remarks" />
                <textarea id="remarks" name="remarks" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 text-sm">{{ old('remarks', $record->remarks) }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('borrow-records.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</a>
                <x-primary-button>Update Record</x-primary-button>
            </div>
        </form>
    </div>
</x-layouts.app>