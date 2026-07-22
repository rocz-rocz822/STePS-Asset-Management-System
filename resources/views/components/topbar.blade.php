<header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 gap-4">
    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600 shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <!-- Global Search -->
    <div class="relative flex-1 max-w-md" x-data="globalSearch()">
        <input
            type="text"
            x-model="query"
            @input.debounce.300ms="search()"
            @focus="showResults = true"
            @click.outside="showResults = false"
            placeholder="Search assets, borrowers, maintenance..."
            class="w-full rounded-lg border-gray-300 text-sm focus:border-slate-500 focus:ring-slate-500"
        >

        <div x-show="showResults && (results.length > 0 || loading)" x-cloak
             class="absolute mt-1 w-full bg-white rounded-lg shadow-lg border border-gray-100 max-h-96 overflow-y-auto z-50">
            <div x-show="loading" class="px-4 py-3 text-sm text-gray-400">Searching...</div>
            <template x-for="result in results" :key="result.url + result.title">
                <a :href="result.url" class="block px-4 py-2.5 hover:bg-gray-50 border-b border-gray-50 last:border-0">
                    <span class="text-xs text-gray-400 uppercase" x-text="result.type"></span>
                    <p class="text-sm font-medium text-gray-900" x-text="result.title"></p>
                    <p class="text-xs text-gray-500" x-text="result.subtitle"></p>
                </a>
            </template>
        </div>
    </div>

    <div class="flex items-center gap-4 shrink-0" x-data="{ open: false }">
        <button @click="open = !open" class="flex items-center gap-2 text-sm font-medium text-gray-700">
            <span class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center text-xs font-semibold">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </span>
            <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 capitalize">{{ auth()->user()->role }}</span>
        </button>

        <div x-show="open" @click.outside="open = false" x-cloak
             class="absolute right-4 top-14 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1">
            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</header>

@push('scripts')
<script>
    function globalSearch() {
        return {
            query: '',
            results: [],
            loading: false,
            showResults: false,
            async search() {
                if (this.query.length < 2) {
                    this.results = [];
                    return;
                }
                this.loading = true;
                const res = await fetch(`{{ route('search') }}?q=${encodeURIComponent(this.query)}`);
                const data = await res.json();
                this.results = data.results;
                this.loading = false;
            }
        }
    }
</script>
@endpush