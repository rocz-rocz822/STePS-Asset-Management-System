```blade
<x-layouts.app title="Add User">

    <x-page-heading
        title="Add User"
        subtitle="Create a new IT department account."
    />

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 max-w-2xl">

        <form
            method="POST"
            action="{{ route('users.store') }}"
            class="space-y-5"
            x-data="{ role: '{{ old('role', 'technician') }}' }"
        >
            @csrf

            {{-- Full Name --}}
            <div>
                <x-input-label for="name" value="Full Name" />

                <x-text-input
                    id="name"
                    name="name"
                    type="text"
                    class="mt-1 block w-full"
                    :value="old('name')"
                    placeholder="Enter full name"
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
                <x-input-label for="email" value="Email Address" />

                <x-text-input
                    id="email"
                    name="email"
                    type="email"
                    class="mt-1 block w-full"
                    :value="old('email')"
                    placeholder="Enter email address"
                    required
                />

                <x-input-error
                    :messages="$errors->get('email')"
                    class="mt-2"
                />
            </div>

            {{-- Password --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <x-input-label for="password" value="Password" />

                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        class="mt-1 block w-full"
                        required
                    />

                    <p class="text-xs text-gray-400 mt-1">
                        Minimum 12 characters with uppercase, lowercase,
                        a number, and a symbol.
                    </p>

                    <x-input-error
                        :messages="$errors->get('password')"
                        class="mt-2"
                    />
                </div>

                {{-- Confirm Password --}}
                <div>
                    <x-input-label
                        for="password_confirmation"
                        value="Confirm Password"
                    />

                    <x-text-input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        required
                    />

                    <x-input-error
                        :messages="$errors->get('password_confirmation')"
                        class="mt-2"
                    />
                </div>

            </div>

            {{-- Role --}}
            <div>
                <x-input-label for="role" value="Role" />

                <select
                    id="role"
                    name="role"
                    x-model="role"
                    class="mt-1 block w-full rounded-lg border-gray-300
                           text-sm focus:border-indigo-500 focus:ring-indigo-500"
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

                <x-input-error
                    :messages="$errors->get('role')"
                    class="mt-2"
                />
            </div>

            {{-- Role Information --}}
            <div
                x-show="role === 'technician'"
                x-cloak
                class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-3"
            >
                <p class="text-sm text-gray-700 font-medium">
                    Technician Account
                </p>

                <p class="text-xs text-gray-500 mt-1">
                    This account will have technician-level access to the
                    asset management system.
                </p>
            </div>

            <div
                x-show="role === 'admin'"
                x-cloak
                class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-3"
            >
                <p class="text-sm text-yellow-800 font-medium">
                    Administrator Account
                </p>

                <p class="text-xs text-yellow-700 mt-1">
                    Administrators have full access to the system,
                    including asset management and user administration.
                </p>
            </div>

            <div
                x-show="role === 'staff'"
                x-cloak
                class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-3"
            >
                <p class="text-sm text-blue-800 font-medium">
                    Staff Account
                </p>

                <p class="text-xs text-blue-700 mt-1">
                    Staff can view and manage assets assigned to their
                    account.
                </p>
            </div>

            {{-- Active Account --}}
            <div class="flex items-center gap-2 pt-1">

                <input
                    type="checkbox"
                    id="is_active"
                    name="is_active"
                    value="1"
                    @checked(old('is_active', true))
                    class="rounded border-gray-300
                           text-indigo-600 focus:ring-indigo-500"
                >

                <x-input-label
                    for="is_active"
                    value="Account is active"
                />

            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">

                <a
                    href="{{ route('users.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700
                        bg-gray-100 rounded-lg
                        hover:bg-gray-200 transition"
                >
                    Cancel
                </a>

                <x-primary-button>
                    Create User
                </x-primary-button>

            </div>

        </form>

    </div>

</x-layouts.app>
```
