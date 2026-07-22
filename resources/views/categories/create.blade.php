<x-layouts.app title="Add Category">
    <x-page-heading title="Add Category" />

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 max-w-xl">
        <form method="POST" action="{{ route('categories.store') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="name" value="Category Name" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="description" value="Description (optional)" />
                <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 text-sm">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="is_active" name="is_active" value="1" checked class="rounded border-gray-300">
                <x-input-label for="is_active" value="Active" />
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('categories.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</a>
                <x-primary-button>Create Category</x-primary-button>
            </div>
        </form>
    </div>
</x-layouts.app>