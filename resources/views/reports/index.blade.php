<x-layouts.app title="Reports">
    <x-page-heading title="Reports" subtitle="Generate and export inventory reports." />

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($types as $type)
            <a href="{{ route('reports.show', $type->value) }}"
               class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:border-slate-300 hover:shadow-md transition">
                <h3 class="text-sm font-semibold text-gray-900">{{ $type->label() }}</h3>
                <p class="text-xs text-gray-500 mt-1">{{ $type->description() }}</p>
            </a>
        @endforeach
    </div>
</x-layouts.app>