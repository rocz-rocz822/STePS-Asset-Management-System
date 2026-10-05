<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.jpg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-gray-50 font-sans antialiased" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex">

        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main content -->
        <div class="flex-1 flex flex-col lg:pl-64">

            <x-topbar />

            <main class="flex-1 p-4 sm:p-6 lg:p-8">

                {{ $slot }}

            </main>

        </div>

    </div>

    @if (session('error'))

        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-4 right-4 bg-red-600 text-white px-4 py-3 rounded-lg shadow-lg z-50"
        >
            {{ session('error') }}
        </div>

    @endif

    @auth
    <script>
        (function () {
            const TIMEOUT_MS = 60 * 1000; // 1 minute
            let idleTimer;

            function logout() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('logout') }}";

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = "{{ csrf_token() }}";
                form.appendChild(csrf);

                document.body.appendChild(form);
                form.submit();
            }

            function resetTimer() {
                clearTimeout(idleTimer);
                idleTimer = setTimeout(logout, TIMEOUT_MS);
            }

            ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart', 'click'].forEach(function (event) {
                document.addEventListener(event, resetTimer, { passive: true });
            });

            resetTimer();
        })();
    </script>
    @endauth

    @stack('scripts')

</body>

</html>