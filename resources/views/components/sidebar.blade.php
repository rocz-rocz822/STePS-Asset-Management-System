<aside
    class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 text-slate-200 transform transition-transform duration-200 ease-in-out
            -translate-x-full lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
>

    <div class="flex items-center h-16 px-6 border-b border-slate-800">
        <span class="text-lg font-semibold text-white">
            STePS Assets Inventory
        </span>
    </div>

    <nav class="mt-4 px-2 space-y-1">

        {{-- Dashboard --}}
        <a
            href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium
                {{ request()->routeIs('dashboard')
                    ? 'bg-slate-800 text-white'
                    : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
        >
            Dashboard
        </a>

        {{-- Asset Management --}}
        <div class="pt-4 pb-1 px-4 text-xs font-semibold uppercase text-slate-500">
            Asset Management
        </div>

        {{-- Assets --}}
        @if (auth()->user()->isAdmin())

            {{-- All Assets --}}
            <a
                href="{{ route('assets.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium
                    {{ request()->routeIs('assets.*') && ! request('mine')
                        ? 'bg-slate-800 text-white'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                All Assets
            </a>

            {{-- My Assets --}}
            <a
                href="{{ route('assets.index', ['mine' => 1]) }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium
                    {{ request()->routeIs('assets.*') && request('mine')
                        ? 'bg-slate-800 text-white'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                My Assets
            </a>

        @else

            {{-- Technician / Staff: their assigned assets are their entire list --}}
            <a
                href="{{ route('assets.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium
                    {{ request()->routeIs('assets.*')
                        ? 'bg-slate-800 text-white'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                My Assets
            </a>

        @endif

        {{-- Assignment Log --}}
        <a
            href="{{ route('assignment-log.index') }}"
            class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium
                {{ request()->routeIs('assignment-log.*')
                    ? 'bg-slate-800 text-white'
                    : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
        >
            Assignment Log
        </a>

        {{-- Borrowing --}}
        @can('viewAny', \App\Models\BorrowRecord::class)

            <a
                href="{{ route('borrow-records.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium
                    {{ request()->routeIs('borrow-records.*')
                        ? 'bg-slate-800 text-white'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                Borrowing
            </a>

        @endcan

        {{-- Technician / Admin Menu --}}
        @if (! auth()->user()->isStaff())

            {{-- Asset History --}}
            <a
                href="{{ route('asset-histories.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium
                    {{ request()->routeIs('asset-histories.*')
                        ? 'bg-slate-800 text-white'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                Asset History
            </a>

            {{-- Activity Logs --}}
            <a
                href="{{ route('activity-logs.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium
                    {{ request()->routeIs('activity-logs.*')
                        ? 'bg-slate-800 text-white'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                Activity Logs
            </a>

            {{-- Maintenance --}}
            <a
                href="{{ route('maintenance-records.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium
                    {{ request()->routeIs('maintenance-records.*')
                        ? 'bg-slate-800 text-white'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                Maintenance
            </a>

        @endif

        {{-- Admin-only Management --}}
        @if (auth()->user()->isAdmin())

            {{-- Categories --}}
            <a
                href="{{ route('categories.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium
                    {{ request()->routeIs('categories.*')
                        ? 'bg-slate-800 text-white'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                Categories
            </a>

            {{-- Locations --}}
            <a
                href="{{ route('locations.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium
                    {{ request()->routeIs('locations.*')
                        ? 'bg-slate-800 text-white'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                Locations
            </a>

            {{-- Brands --}}
            <a
                href="{{ route('brands.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium
                    {{ request()->routeIs('brands.*')
                        ? 'bg-slate-800 text-white'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                Brands
            </a>

            {{-- Suppliers --}}
            <a
                href="{{ route('suppliers.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium
                    {{ request()->routeIs('suppliers.*')
                        ? 'bg-slate-800 text-white'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                Suppliers
            </a>

            {{-- Administration --}}
            <div class="pt-4 pb-1 px-4 text-xs font-semibold uppercase text-slate-500">
                Administration
            </div>

            {{-- Users --}}
            <a
                href="{{ route('users.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium
                    {{ request()->routeIs('users.*')
                        ? 'bg-slate-800 text-white'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                Users
            </a>

            {{-- Deletion Requests --}}
            <a
                href="{{ route('account-deletion-requests.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium
                    {{ request()->routeIs('account-deletion-requests.*')
                        ? 'bg-slate-800 text-white'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                Deletion Requests
            </a>

            {{-- Reports --}}
            <a
                href="{{ route('reports.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium
                    {{ request()->routeIs('reports.*')
                        ? 'bg-slate-800 text-white'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >
                Reports
            </a>

        @endif

    </nav>
</aside>

{{-- Mobile overlay --}}
<div
    x-show="sidebarOpen"
    x-cloak
    @click="sidebarOpen = false"
    class="fixed inset-0 z-30 bg-black/40 lg:hidden"
></div>