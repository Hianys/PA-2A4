<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Tableau de bord administrateur</h2>
    </x-slot>

    <x-admin-content>
        <h2 class="text-2xl font-semibold mb-6">Liste des utilisateurs</h2>

        <form method="GET" action="{{ route('admin.dashboard') }}" class="mb-4">
            <label for="role" class="text-sm font-medium text-gray-700 mr-2">Filtrer par rôle :</label>
            <select name="role" id="role" onchange="this.form.submit()" class="border-gray-300 rounded">
                <option value="">-- Tous --</option>
                <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="client" {{ $role === 'client' ? 'selected' : '' }}>Client</option>
                <option value="livreur" {{ $role === 'livreur' ? 'selected' : '' }}>Livreur</option>
                <option value="prestataire" {{ $role === 'prestataire' ? 'selected' : '' }}>Prestataire</option>
                <option value="commercant" {{ $role === 'commercant' ? 'selected' : '' }}>Commerçant</option>
            </select>
        </form>


        <table class="w-full table-auto bg-white shadow rounded">
            <thead class="bg-gray-100 text-sm text-gray-700">
            <tr>
                <th class="px-4 py-4 text-left">ID</th>
                <th class="px-4 py-4 text-left">Nom</th>
                <th class="px-1 py-4 text-left">Email</th>
                <th class="px-6 py-4 text-left">Rôle</th>
                <th class="px-6 py-4 text-center">Actions</th>
            </tr>
            </thead>
            <tbody class="text-sm text-gray-800">
            @foreach ($users as $user)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-left">{{ $user->id }}</td>
                        <td class="px-6 py-4 text-left">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-left">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-left capitalize">{{ $user->role }}</td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.users.show', $user->id) }}"
                               class="bg-indigo-600 text-white text-xs px-3 py-2 rounded hover:bg-indigo-700">
                                Détails
                            </a>
                        </td>
                    </tr>
            @endforeach
            </tbody>
        </table>



    </x-admin-content>
</x-app-layout>
