<x-layouts.app title="Access Denied">
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <h1 class="text-4xl font-bold text-gray-900">403</h1>
        <p class="mt-2 text-gray-500">You don't have permission to access this page.</p>
        <a href="{{ route('dashboard') }}" class="mt-6 px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-lg hover:bg-slate-800">
            Back to Dashboard
        </a>
    </div>
</x-layouts.app>