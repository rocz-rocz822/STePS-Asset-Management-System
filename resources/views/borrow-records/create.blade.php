<x-layouts.app title="New Borrow Record">
    <x-page-heading title="New Borrow Record" subtitle="Only assets currently marked Available can be borrowed." />

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 max-w-2xl">
        <form method="POST" action="{{ route('borrow-records.store') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="asset_id" value="Asset" />
                <select id="asset_id" name="asset_id" class="mt-1 block w-full rounded-lg border-gray-300 text-sm" required>
                    <option value="">Select an available asset...</option>
                    @foreach ($assets as $asset)
                        <option value="{{ $asset->id }}" @selected(old('asset_id', $preselectedAssetId) == $asset->id)>{{ $asset->asset_code }} — {{ $asset->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('asset_id')" class="mt-2" />
                @if ($assets->isEmpty())
                    <p class="text-xs text-yellow-600 mt-2">No assets are currently available to borrow.</p>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="borrower_name" value="Borrower Name" />
                    <x-text-input id="borrower_name" name="borrower_name" class="mt-1 block w-full" :value="old('borrower_name')" required />
                    <x-input-error :messages="$errors->get('borrower_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="borrower_department" value="Department (optional)" />
                    <x-text-input id="borrower_department" name="borrower_department" class="mt-1 block w-full" :value="old('borrower_department')" />
                </div>
            </div>

            <div>
                <x-input-label for="borrower_contact" value="Contact Number (optional)" />
                <x-text-input id="borrower_contact" name="borrower_contact" class="mt-1 block w-full" :value="old('borrower_contact')" />
            </div>

            <div>
                <x-input-label for="purpose" value="Purpose" />
                <textarea id="purpose" name="purpose" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 text-sm">{{ old('purpose') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="borrow_date" value="Borrow Date" />
                    <x-text-input id="borrow_date" name="borrow_date" type="date" class="mt-1 block w-full" :value="old('borrow_date', now()->format('Y-m-d'))" required />
                    <x-input-error :messages="$errors->get('borrow_date')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="expected_return_date" value="Expected Return Date" />
                    <x-text-input id="expected_return_date" name="expected_return_date" type="date" class="mt-1 block w-full" :value="old('expected_return_date')" required />
                    <x-input-error :messages="$errors->get('expected_return_date')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="remarks" value="Remarks (optional)" />
                <textarea id="remarks" name="remarks" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 text-sm">{{ old('remarks') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('borrow-records.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</a>
                <x-primary-button>Record Borrow</x-primary-button>
            </div>
        </form>
    </div>
</x-layouts.app>