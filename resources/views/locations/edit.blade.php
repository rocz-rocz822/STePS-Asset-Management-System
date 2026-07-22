<x-layouts.app title="Edit Location">
    <x-page-heading title="Edit Location" :subtitle="$location->full_name" />

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 max-w-xl">
        <form method="POST" action="{{ route('locations.update', $location) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="building" value="Building" />
                <x-text-input id="building" name="building" type="text" class="mt-1 block w-full" :value="old('building', $location->building)" required autofocus />
                <x-input-error :messages="$errors->get('building')" class="mt-2" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="floor" value="Floor (optional)" />
                    <x-text-input id="floor" name="floor" type="text" class="mt-1 block w-full" :value="old('floor', $location->floor)" />
                </div>
                <div>
                    <x-input-label for="room" value="Room (optional)" />
                    <x-text-input id="room" name="room" type="text" class="mt-1 block w-full" :value="old('room', $location->room)" />
                </div>
            </div>

            <div>
                <x-input-label for="storage_area" value="Storage Area (optional)" />
                <x-text-input id="storage_area" name="storage_area" type="text" class="mt-1 block w-full" :value="old('storage_area', $location->storage_area)" />
            </div>

            <div>
                <x-input-label for="description" value="Description (optional)" />
                <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 text-sm">{{ old('description', $location->description) }}</textarea>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $location->is_active)) class="rounded border-gray-300">
                <x-input-label for="is_active" value="Active" />
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('locations.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</a>
                <x-primary-button>Save Changes</x-primary-button>
            </div>
        </form>
    </div>
</x-layouts.app>