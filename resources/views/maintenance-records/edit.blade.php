<x-layouts.app title="Manage Maintenance Record">
    <x-page-heading title="Manage Maintenance Record" :subtitle="$record->asset->asset_code" />

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 max-w-2xl">
        <form method="POST" action="{{ route('maintenance-records.update', $record) }}" enctype="multipart/form-data" class="space-y-5" x-data="{ status: '{{ old('status', $record->status->value) }}' }">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="maintenance_date" value="Maintenance Date" />
                    <x-text-input id="maintenance_date" name="maintenance_date" type="date" class="mt-1 block w-full" :value="old('maintenance_date', $record->maintenance_date->format('Y-m-d'))" required />
                </div>
                <div>
                    <x-input-label for="technician_id" value="Technician" />
                    <select id="technician_id" name="technician_id" class="mt-1 block w-full rounded-lg border-gray-300 text-sm" required>
                        @foreach ($technicians as $tech)
                            <option value="{{ $tech->id }}" @selected(old('technician_id', $record->technician_id) == $tech->id)>{{ $tech->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <x-input-label for="issue" value="Issue" />
                <textarea id="issue" name="issue" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 text-sm" required>{{ old('issue', $record->issue) }}</textarea>
            </div>

            <div>
                <x-input-label for="resolution" value="Resolution" />
                <textarea id="resolution" name="resolution" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 text-sm">{{ old('resolution', $record->resolution) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="cost" value="Cost (₱)" />
                    <input
                        type="text"
                        id="cost_display"
                        inputmode="decimal"
                        placeholder="0.00"
                        class="mt-1 block w-full rounded-lg border-gray-300 text-sm"
                        value="{{ old('cost', $record->cost ? number_format($record->cost, 2) : '') }}"
                        x-data
                        x-on:input="
                            let raw = $el.value.replace(/[^\d.]/g, '');
                            let parts = raw.split('.');
                            if (parts.length > 2) parts = [parts[0], parts.slice(1).join('')];
                            let intPart = parts[0].replace(/^0+(?=\d)/, '');
                            let formatted = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                            if (parts[1] !== undefined) formatted += '.' + parts[1].slice(0, 2);
                            $el.value = formatted;
                            document.getElementById('cost').value = raw;
                        "
                    >
                    <input type="hidden" id="cost" name="cost" value="{{ old('cost', $record->cost) }}">
                    <x-input-error :messages="$errors->get('cost')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status" x-model="status" class="mt-1 block w-full rounded-lg border-gray-300 text-sm" required>
                        @foreach (\App\Enums\MaintenanceStatus::cases() as $s)
                            <option value="{{ $s->value }}">{{ $s->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div x-show="status === 'pending' || status === 'in_progress'" x-cloak>
                <x-input-label for="resulting_asset_status" value="Set Asset Status To" />
                <select id="resulting_asset_status" name="resulting_asset_status" class="mt-1 block w-full rounded-lg border-gray-300 text-sm">
                    <option value="under_maintenance" @selected(old('resulting_asset_status', $record->resulting_asset_status) === 'under_maintenance')>Under Maintenance</option>
                    <option value="under_repair" @selected(old('resulting_asset_status', $record->resulting_asset_status) === 'under_repair')>Under Repair</option>
                </select>
                <p class="text-xs text-gray-400 mt-1">Marking Completed or Cancelled will automatically revert the asset to its prior status.</p>
            </div>

            <div>
                <x-input-label for="remarks" value="Remarks" />
                <textarea id="remarks" name="remarks" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 text-sm">{{ old('remarks', $record->remarks) }}</textarea>
            </div>

            <div>
                <x-input-label for="attachments" value="Add More Attachments (optional)" />
                <input type="file" name="attachments[]" multiple class="mt-1 block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-gray-100 file:text-sm">

                @if ($record->attachments->isNotEmpty())
                    <ul class="mt-3 space-y-1">
                        @foreach ($record->attachments as $attachment)
                            <li class="text-sm"><a href="{{ $attachment->url }}" target="_blank" class="text-slate-700 hover:underline">{{ $attachment->original_name }}</a></li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('maintenance-records.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</a>
                <x-primary-button>Update Record</x-primary-button>
            </div>
        </form>
    </div>
</x-layouts.app>