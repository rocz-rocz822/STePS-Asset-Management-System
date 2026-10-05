<x-layouts.app title="Edit User">
    <x-page-heading
        title="Edit User"
        :subtitle="$user->name"
    />

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 max-w-2xl">

        <form
            method="POST"
            action="{{ route('users.update', $user) }}"
            class="space-y-5"
            x-data="{ role: '{{ old('role', $user->role) }}' }"
        >
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div>
                <x-input-label for="name" value="Full Name" />

                <x-text-input
                    id="name"
                    name="name"
                    type="text"
                    class="mt-1 block w-full"
                    :value="old('name', $user->name)"
                    required
                    autofocus
                />

                <x-input-error
                    :messages="$errors->get('name')"
                    class="mt-2"
                />
            </div>

            {{-- Email --}}
            <div>
                <x-input-label for="email" value="Email Address *" />

                <x-text-input
                    id="email"
                    name="email"
                    type="email"
                    class="mt-1 block w-full"
                    :value="old('email', $user->email)"
                    required
                />

                <x-input-error
                    :messages="$errors->get('email')"
                    class="mt-2"
                />
            </div>

            {{-- Password --}}
            <div class="grid grid-cols-2 gap-4">

                <div>
                    <x-input-label
                        for="password"
                        value="New Password (optional)"
                    />

                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        class="mt-1 block w-full"
                    />

                    <x-input-error
                        :messages="$errors->get('password')"
                        class="mt-2"
                    />
                </div>

                <div>
                    <x-input-label
                        for="password_confirmation"
                        value="Confirm New Password"
                    />

                    <x-text-input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                    />
                </div>

            </div>

            {{-- Role --}}
            <div>

                <x-input-label for="role" value="Role" />

                @if ($user->is_protected)

                    <select
                        id="role"
                        name="role"
                        class="mt-1 block w-full rounded-lg border-gray-300 text-sm bg-gray-50"
                        disabled
                    >
                        <option value="admin" selected>
                            Administrator (Protected)
                        </option>
                    </select>

                    <input
                        type="hidden"
                        name="role"
                        value="admin"
                    >

                    <p class="mt-1 text-xs text-gray-400">
                        This is a protected system account.
                        Its role cannot be changed.
                    </p>

                @else

                    <select
                        id="role"
                        name="role"
                        x-model="role"
                        class="mt-1 block w-full rounded-lg border-gray-300 text-sm"
                        required
                    >
                        <option value="technician">
                            Technician
                        </option>

                        <option value="admin">
                            Administrator
                        </option>

                        <option value="staff">
                            Staff
                        </option>
                    </select>

                @endif

                <x-input-error
                    :messages="$errors->get('role')"
                    class="mt-2"
                />

            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-3 pt-4">

                <a
                    href="{{ route('users.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200"
                >
                    Cancel
                </a>

                <x-primary-button>
                    Save Changes
                </x-primary-button>

            </div>

        </form>

    </div>
</x-layouts.app>