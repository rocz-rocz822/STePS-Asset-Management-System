<form method="post" action="{{ route('profile.update') }}" class="space-y-5">
    @csrf
    @method('patch')

    <div>
        <x-input-label for="name" value="Full Name *" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="email" value="Email Address *" />

        @if ($user->google_id)
            <x-text-input
                id="email"
                type="email"
                class="mt-1 block w-full bg-gray-50 text-gray-500"
                value="{{ $user->email }}"
                disabled
            />

            <input type="hidden" name="email" value="{{ $user->email }}">

            <p class="text-xs text-gray-400 mt-1">
                This account signs in with Google. To change the email, unlink Google using the option below this form.
            </p>
        @else
            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                :value="old('email', $user->email)"
                required
                autocomplete="username"
            />

            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        @endif

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-2">
                <p class="text-sm text-yellow-700">
                    Your email address is unverified.
                    <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900">
                        Click here to re-send the verification email.
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 text-sm font-medium text-green-600">
                        A new verification link has been sent to your email address.
                    </p>
                @endif
            </div>
        @endif
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Save Changes</x-primary-button>

        @if (session('status') === 'profile-updated')
            <p class="text-sm text-gray-500">Saved.</p>
        @endif
    </div>
</form>

@if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>
@endif