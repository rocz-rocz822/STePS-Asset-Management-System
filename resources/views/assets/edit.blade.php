<x-layouts.app title="Edit Asset">
    <x-page-heading title="Edit Asset" :subtitle="$asset->asset_code . ' — ' . $asset->name" />

    <form method="POST" action="{{ route('assets.update', $asset) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('assets._form')

        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('assets.show', $asset) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</a>
            <x-primary-button>Save Changes</x-primary-button>
        </div>
    </form>
</x-layouts.app>