<x-layouts.app title="Add Asset">
    <x-page-heading title="Add Asset" subtitle="Register a new IT asset into the inventory." />

    <form method="POST" action="{{ route('assets.store') }}" enctype="multipart/form-data">
        @csrf
        @include('assets._form')

        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('assets.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</a>
            <x-primary-button>Create Asset</x-primary-button>
        </div>
    </form>
</x-layouts.app>