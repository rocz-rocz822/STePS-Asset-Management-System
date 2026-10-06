<x-layouts.app title="Dashboard">

    <x-page-heading
        title="Dashboard"
        :subtitle="auth()->user()->isAdmin()
            ? 'Welcome back, ' . auth()->user()->name . '. Showing system-wide totals.'
            : 'Welcome back, ' . auth()->user()->name . '. Showing totals for assets assigned to you.'"
    />

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-sm text-gray-500">Total Assets</p>
            <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $stats['total'] }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-sm text-gray-500">Available</p>
            <p class="text-2xl font-semibold text-green-600 mt-1">{{ $stats['available'] }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-sm text-gray-500">Assigned</p>
            <p class="text-2xl font-semibold text-blue-600 mt-1">{{ $stats['assigned'] }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-sm text-gray-500">Borrowed</p>
            <p class="text-2xl font-semibold text-yellow-600 mt-1">{{ $stats['borrowed'] }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-sm text-gray-500">Under Maintenance</p>
            <p class="text-2xl font-semibold text-yellow-600 mt-1">{{ $stats['under_maintenance'] }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-sm text-gray-500">Under Repair</p>
            <p class="text-2xl font-semibold text-yellow-600 mt-1">{{ $stats['under_repair'] }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-sm text-gray-500">Disposed</p>
            <p class="text-2xl font-semibold text-red-600 mt-1">{{ $stats['disposed'] }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-sm text-gray-500">Warranty Expiring Soon</p>
            <p class="text-2xl font-semibold text-orange-600 mt-1">{{ $stats['warranty_expiring_soon'] }}</p>
        </div>

        @unless (auth()->user()->isAdmin())
            <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
                <p class="text-sm text-gray-500">Assets I've Added</p>
                <p class="text-2xl font-semibold text-slate-900 mt-1">
                    {{ $stats['mine'] }}
                </p>

                <a
                    href="{{ route('assets.index', ['mine' => 1]) }}"
                    class="text-xs text-slate-500 hover:underline mt-1 inline-block"
                >
                    View my records →
                </a>
            </div>
        @endunless

    </div>

    <div class="flex flex-wrap gap-3 mb-6">

        <a
            href="{{ route('assets.create') }}"
            class="px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-lg hover:bg-slate-800"
        >
            + Add Asset
        </a>

        <a
            href="{{ route('assets.index') }}"
            class="px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50"
        >
            Search Assets
        </a>

        @if (auth()->user()->isAdmin())
            <a
                href="{{ route('reports.index') }}"
                class="px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50"
            >
                Reports
            </a>
        @endif

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Assets by Category</h3>
            <canvas id="categoryChart" height="220"></canvas>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Assets by Status</h3>
            <canvas id="statusChart" height="220"></canvas>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Assets by Location</h3>
            <canvas id="locationChart" height="220"></canvas>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Recently Added Assets</h3>

            @forelse ($recentAssets as $asset)
                <div class="flex items-center justify-between text-sm border-b border-gray-50 py-2 last:border-0">
                    <div>
                        <a
                            href="{{ route('assets.show', $asset) }}"
                            class="font-medium text-gray-900 hover:underline"
                        >
                            {{ $asset->name }}
                        </a>

                        <span class="block text-xs text-gray-400">
                            {{ $asset->asset_code }} · {{ $asset->category->name }}
                        </span>
                    </div>

                    <span class="text-xs text-gray-400">
                        {{ $asset->created_at->diffForHumans() }}
                    </span>
                </div>
            @empty
                <p class="text-sm text-gray-400">No assets yet.</p>
            @endforelse
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Recent Activities</h3>

            @forelse ($recentActivities as $activity)
                <div class="flex items-center justify-between text-sm border-b border-gray-50 py-2 last:border-0">
                    <div>
                        <span class="font-medium text-gray-900">
                            {{ $activity->causer?->name ?? 'System' }}
                        </span>

                        <span class="text-gray-500">
                            {{ $activity->description }}
                        </span>
                    </div>

                    <span class="text-xs text-gray-400">
                        {{ $activity->created_at->diffForHumans() }}
                    </span>
                </div>
            @empty
                <p class="text-sm text-gray-400">No recent activity.</p>
            @endforelse
        </div>

    </div>

    @push('scripts')

        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>

        <script>
            const palette = [
                '#0f172a',
                '#475569',
                '#94a3b8',
                '#cbd5e1',
                '#1e293b',
                '#64748b'
            ];

            new Chart(document.getElementById('categoryChart'), {
                type: 'bar',
                data: {
                    labels: @json($byCategory->pluck('name')),
                    datasets: [{
                        data: @json($byCategory->pluck('total')),
                        backgroundColor: palette[0]
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });

            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: @json($byStatus->pluck('label')),
                    datasets: [{
                        data: @json($byStatus->pluck('total')),
                        backgroundColor: palette
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });

            new Chart(document.getElementById('locationChart'), {
                type: 'bar',
                data: {
                    labels: @json($byLocation->pluck('building')),
                    datasets: [{
                        data: @json($byLocation->pluck('total')),
                        backgroundColor: palette[1]
                    }]
                },
                options: {
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        </script>

    @endpush

</x-layouts.app>