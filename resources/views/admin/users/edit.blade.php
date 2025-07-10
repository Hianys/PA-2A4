<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Modifier utilisateur</h2>
    </x-slot>

    <div class="max-w-xl mx-auto mt-8">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700">Nom</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                       class="mt-1 block w-full rounded border-gray-300">
                @error('name')
                <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                       class="mt-1 block w-full rounded border-gray-300">
                @error('email')
                <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Rôle</label>
                <select name="role" class="mt-1 block w-full rounded border-gray-300">
                    @foreach (['client','livreur','prestataire','commercant'] as $role)
                        <option value="{{ $role }}"
                            {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                            {{ ucfirst($role) }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-end items-center gap-4 mt-4">
                <a href="{{ route('admin.users.show', $user->id) }}"
                   class="border border-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-100">
                    Annuler
                </a>
                <button type="submit"
                        class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Enregistrer
                </button>
            </div>

        </form>
    </div>
</x-app-layout>
