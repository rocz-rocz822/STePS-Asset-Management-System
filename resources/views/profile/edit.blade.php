<x-layouts.app title="Profile">
    <x-page-heading title="Profile" subtitle="Manage your account information and security settings." />

    @if (session('status') === 'google-linked')
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-2.5">
            Google account linked successfully.
        </div>
    @elseif (session('status') === 'google-link-mismatch')
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-2.5">
            That Google account's email doesn't match this account. Sign in with the Google account that uses this exact email address.
        </div>
    @elseif (session('status') === 'google-link-conflict')
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-2.5">
            That Google account is already linked to a different user.
        </div>
    @elseif (session('status') === 'deletion-requested')
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

                        @if (auth()->user()->google_id)
                            <x-badge color="green">
                                Google Linked
                            </x-badge>
                        @endif
                    </div>
                </div>
            </div>

            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Google Account --}}
        @if (auth()->user()->google_id)
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-6">
                <h3 class="text-sm font-semibold text-blue-800 mb-1">
                    Google Account Linked
                </h3>

                <p class="text-xs text-blue-700 mb-4">
                    This account signs in with Google. To change the email address, unlink Google first — you'll need a working password to sign in afterward, or you can re-link by signing in with Google again using the new email.
                </p>

                <form
                    method="POST"
                    action="{{ route('profile.unlink-google') }}"
                    onsubmit="return confirm('Unlink your Google account? Make sure you have a working password set before continuing.');"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="px-4 py-2 bg-white border border-blue-200 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-100"
                    >
                        Unlink Google Account
                    </button>
                </form>

                @if (session('status') === 'google-unlinked')
                    <p class="mt-3 text-sm font-medium text-green-700">
                        Google account unlinked. You can now edit your email in the form above, or sign in with Google again to re-link.
                    </p>
                @endif
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-1">
                    Link Google Account
                </h3>

                <p class="text-xs text-gray-500 mb-4">
                    Link your @ust.edu.ph Google account to sign in without a password. It must match this account's email:
                    <strong>{{ auth()->user()->email }}</strong>.
                </p>

                <a
                    href="{{ route('auth.google.link') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    <svg class="w-4 h-4" viewBox="0 0 48 48">
                        <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3c-1.6 4.7-6.1 8-11.3 8-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.8 1.1 8 3l5.7-5.7C34.6 6.1 29.6 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.7-.4-3.5z"/>
                        <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.5 15.1 18.9 12 24 12c3.1 0 5.8 1.1 8 3l5.7-5.7C34.6 6.1 29.6 4 24 4 16.3 4 9.7 8.3 6.3 14.7z"/>
                        <path fill="#4CAF50" d="M24 44c5.5 0 10.5-2.1 14.2-5.6l-6.6-5.6C29.5 34.6 26.9 35.5 24 35.5c-5.2 0-9.6-3.3-11.3-7.9l-6.6 5.1C9.5 39.6 16.2 44 24 44z"/>
                        <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.3-2.2 4.3-4.1 5.8l6.6 5.6C41.9 35.9 44 30.4 44 24c0-1.3-.1-2.7-.4-3.5z"/>
                    </svg>

                    Link Google Account
                </a>
            </div>
        @endif

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