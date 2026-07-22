@php $asset = $asset ?? null; @endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 space-y-6">

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Basic Information</h3>

            <div class="space-y-4">
                <div>
                    <x-input-label for="name" value="Asset Name" />
                    <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $asset->name ?? '')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" value="Description" />
                    <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 text-sm">{{ old('description', $asset->description ?? '') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="category_id" value="Category" />
                        <select id="category_id" name="category_id" class="mt-1 block w-full rounded-lg border-gray-300 text-sm" required>
                            <option value="">Select category...</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $asset->category_id ?? '') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="location_id" value="Location" />
                        <select id="location_id" name="location_id" class="mt-1 block w-full rounded-lg border-gray-300 text-sm" required>
                            <option value="">Select location...</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}" @selected(old('location_id', $asset->location_id ?? '') == $location->id)>{{ $location->full_name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('location_id')" class="mt-2" />
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Identification</h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="brand" value="Brand" />
                    <x-text-input id="brand" name="brand" class="mt-1 block w-full" :value="old('brand', $asset->brand ?? '')" />
                </div>
                <div>
                    <x-input-label for="model" value="Model" />
                    <x-text-input id="model" name="model" class="mt-1 block w-full" :value="old('model', $asset->model ?? '')" />
                </div>
                <div>
                    <x-input-label for="serial_number" value="Serial Number" />
                    <x-text-input id="serial_number" name="serial_number" class="mt-1 block w-full" :value="old('serial_number', $asset->serial_number ?? '')" />
                    <x-input-error :messages="$errors->get('serial_number')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="property_number" value="Property Number" />
                    <x-text-input id="property_number" name="property_number" class="mt-1 block w-full" :value="old('property_number', $asset->property_number ?? '')" />
                </div>
                <div>
                    <x-input-label for="inventory_number" value="Inventory Number" />
                    <x-text-input id="inventory_number" name="inventory_number" class="mt-1 block w-full" :value="old('inventory_number', $asset->inventory_number ?? '')" />
                </div>
                <div>
                    <x-input-label for="manufacturer" value="Manufacturer" />
                    <x-text-input id="manufacturer" name="manufacturer" class="mt-1 block w-full" :value="old('manufacturer', $asset->manufacturer ?? '')" />
                </div>
                <div>
                    <x-input-label for="supplier" value="Supplier" />
                    <x-text-input id="supplier" name="supplier" class="mt-1 block w-full" :value="old('supplier', $asset->supplier ?? '')" />
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Purchase & Warranty</h3>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <x-input-label for="purchase_date" value="Purchase Date" />
                    <x-text-input id="purchase_date" name="purchase_date" type="date" class="mt-1 block w-full" :value="old('purchase_date', optional($asset->purchase_date ?? null)->format('Y-m-d'))" />
                    <x-input-error :messages="$errors->get('purchase_date')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="purchase_cost" value="Purchase Cost (₱)" />
                    <x-text-input id="purchase_cost" name="purchase_cost" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('purchase_cost', $asset->purchase_cost ?? '')" />
                    <x-input-error :messages="$errors->get('purchase_cost')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="warranty_expiration" value="Warranty Expiration" />
                    <x-text-input id="warranty_expiration" name="warranty_expiration" type="date" class="mt-1 block w-full" :value="old('warranty_expiration', optional($asset->warranty_expiration ?? null)->format('Y-m-d'))" />
                    <x-input-error :messages="$errors->get('warranty_expiration')" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Status & Assignment</h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status" class="mt-1 block w-full rounded-lg border-gray-300 text-sm" required>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(old('status', $asset->status->value ?? 'available') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="condition" value="Condition" />
                    <select id="condition" name="condition" class="mt-1 block w-full rounded-lg border-gray-300 text-sm" required>
                        @foreach ($conditions as $condition)
                            <option value="{{ $condition->value }}" @selected(old('condition', $asset->condition->value ?? 'good') === $condition->value)>{{ $condition->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <x-input-label for="assigned_to" value="Assigned User (optional)" />
                    <select id="assigned_to" name="assigned_to" class="mt-1 block w-full rounded-lg border-gray-300 text-sm">
                        <option value="">Unassigned</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(old('assigned_to', $asset->assigned_to ?? '') == $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <x-input-label for="remarks" value="Remarks" />
                    <textarea id="remarks" name="remarks" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 text-sm">{{ old('remarks', $asset->remarks ?? '') }}</textarea>
                </div>
                @if (isset($asset))
                <div class="col-span-2">
                    <x-input-label for="change_remarks" value="Reason for This Change (optional)" />
                    <textarea id="change_remarks" name="change_remarks" rows="2" placeholder="e.g. Reassigned after employee transfer"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm"></textarea>
                    <p class="text-xs text-gray-400 mt-1">This note is attached to the asset history entry for this update.</p>
                </div>
            @endif
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Photo</h3>

            @if (! empty($asset?->photo_path))
                <img src="{{ Storage::url($asset->photo_path) }}" class="w-full h-40 object-cover rounded-lg mb-3 border border-gray-100">
                <p class="text-xs text-gray-400 mb-3">Uploading a new photo will replace this one.</p>
            @endif

            <input type="file" name="photo" accept="image/*" class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-gray-100 file:text-sm">
            <x-input-error :messages="$errors->get('photo')" class="mt-2" />
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Attachments</h3>
            <p class="text-xs text-gray-500 mb-3">Warranty cards, manuals, receipts (PDF, JPG, PNG, DOC — max 8MB each).</p>

            <input type="file" name="attachments[]" multiple class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-gray-100 file:text-sm">
            <x-input-error :messages="$errors->get('attachments.0')" class="mt-2" />

            @if (! empty($asset?->attachments) && $asset->attachments->isNotEmpty())
                <ul class="mt-4 space-y-2">
                    @foreach ($asset->attachments as $attachment)
                        <li class="flex items-center justify-between text-sm bg-gray-50 rounded-lg px-3 py-2">
                            <a href="{{ $attachment->url }}" target="_blank" class="text-slate-700 hover:underline truncate">{{ $attachment->original_name }}</a>
                            <button type="button" @click="$dispatch('open-modal-delete-attachment-{{ $attachment->id }}')" class="text-red-500 hover:text-red-700 text-xs ml-2">Remove</button>
                            <x-confirm-modal
                                id="delete-attachment-{{ $attachment->id }}"
                                title="Remove this attachment?"
                                :action="route('assets.attachments.destroy', [$asset, $attachment])"
                            />
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>