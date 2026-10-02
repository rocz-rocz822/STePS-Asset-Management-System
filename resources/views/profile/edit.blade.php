<x-layouts.app title="Profile">
    <x-page-heading title="Profile" subtitle="Manage your account information and security settings." />

    @if (session('status') === 'deletion-requested')
        <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm rounded-lg px-4 py-2.5">
            Your deletion request has been submitted. Your account stays active until an administrator reviews it.
        </div>
    @endif

    <div class="max-w-2xl space-y-6">

        {{-- Profile Information --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center gap-4 mb-6">
                <span class="w-14 h-14 rounded-full bg-slate-900 text-white flex items-center justify-center text-lg font-semibold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>

                <div>
                    <p class="font-semibold text-gray-900">
                        {{ auth()->user()->name }}
                    </p>

                    <div class="flex items-center gap-2 mt-1">
                        <x-badge color="gray" class="capitalize">
                            {{ auth()->user()->role }}
                        </x-badge>

                        @if (auth()->user()->is_protected)
                            <x-badge color="yellow">
                                Protected Account
                            </x-badge>
                        @endif
                    </div>
                </div>
            </div>

            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Update Password --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-1">
                Update Password
            </h3>

            <p class="text-xs text-gray-500 mb-5">
                Ensure your account uses a long, random password to stay secure.
            </p>

            @include('profile.partials.update-password-form')
        </div>

        {{-- Account Deletion --}}
        @unless (auth()->user()->is_protected)
            <div class="bg-white rounded-xl border border-red-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-red-700 mb-1">
                    Delete Account
                </h3>

                <p class="text-xs text-gray-500 mb-5">
                    Submits a request for your administrator to review. Your account is only deactivated upon approval — nothing is permanently deleted.
                </p>

                @include('profile.partials.delete-user-form')
            </div>
        @else
            <div class="bg-gray-50 rounded-xl border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-1">
                    Delete Account
                </h3>

                <p class="text-xs text-gray-500">
                    This is a protected system account and cannot request deletion.
                </p>
            </div>
        @endunless

    </div>
</x-layouts.app>