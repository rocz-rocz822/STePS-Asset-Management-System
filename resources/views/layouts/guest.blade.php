<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'STePS Assets Management System') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">

        <!-- Left branding panel -->
        <div class="hidden lg:flex lg:w-1/2 bg-slate-900 text-white flex-col justify-between p-12 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10"
                style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 32px 32px;"></div>

            <div class="relative">
                <div class="text-2xl font-bold tracking-tight">STePS</div>
                <div class="text-sm text-slate-400 mt-1">Assets Management System</div>
            </div>

            <div class="relative">
                <h1 class="text-3xl font-semibold leading-snug max-w-md">
                    Every STePS asset, tracked from purchase to disposal.
                </h1>
                <p class="text-slate-400 mt-4 max-w-sm text-sm leading-relaxed">
                    A single place for the STePS department to manage assets, assignments, borrowing, maintenance, and reporting.
                </p>
            </div>

            <div class="relative text-xs text-slate-500">
                &copy; {{ now()->year }} STePS Department. Internal use only.
            </div>
        </div>

        <!-- Right form panel -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-sm">

                <div class="lg:hidden text-center mb-8">
                    <div class="text-2xl font-bold text-slate-900">STePS</div>
                    <div class="text-sm text-gray-500">Assets Management System</div>
                </div>

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-8">
                    {{ $slot }}
                </div>

            </div>
        </div>

    </div>
</body>
</html>