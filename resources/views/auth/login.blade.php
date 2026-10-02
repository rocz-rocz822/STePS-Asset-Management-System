<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Sign in</h2>
        <p class="text-sm text-gray-500 mt-1">Welcome back to STePS Assets Management System.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <button type="submit" class="w-full flex justify-center px-4 py-2.5 bg-slate-900 text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition">
            Log in
        </button>
    </form>

    <p class="text-xs text-gray-400 text-center mt-6">
        Access is managed by your system administrator.
    </p>
</x-guest-layout>