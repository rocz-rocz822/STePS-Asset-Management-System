<x-layouts.app title="Add User">
    <x-page-heading title="Add User" subtitle="Create a new IT department account." />

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 max-w-2xl">
        <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="name" value="Full Name" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" value="Email Address" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="password_confirmation" value="Confirm Password" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                </div>
            </div>

            <div>
                <x-input-label for="role" value="Role" />
                <select id="role" name="role" class="mt-1 block w-full rounded-lg border-gray-300 text-sm" required>
                    <option value="technician" @selected(old('role') === 'technician')>IT Staff / Technician</option>
                    <option value="admin" @selected(old('role') === 'admin')>Administrator</option>
                </select>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <div class="flex items-center gap-2">
            <input
                type="checkbox"
                id="can_manage_assets"
                name="can_manage_assets"
                value="1"
                checked
                class="rounded border-gray-300"
            >

            <x-input-label
                for="can_manage_assets"
                value="Can add and edit assets"
            />
        </div>

        <p class="text-xs text-gray-400 -mt-3">
            Uncheck to give this technician view-only access to assets.
        </p>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="is_active" name="is_active" value="1" checked class="rounded border-gray-300">
                <x-input-label for="is_active" value="Account is active" />
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('users.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</a>
                <x-primary-button>Create User</x-primary-button>
            </div>
        </form>
    </div>
</x-layouts.app>