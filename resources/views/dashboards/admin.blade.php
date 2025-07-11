<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Tableau de bord Administrateur</h2>
    </x-slot>

    <x-admin-content>
        <h2 class="text-2xl font-semibold mb-6">Liste des utilisateurs</h2>

        <table class="w-full table-auto bg-white shadow rounded">
            <thead class="bg-gray-100 text-sm text-gray-700">
            <tr>
                <th class="px-4 py-4 text-left">ID</th>
                <th class="px-4 py-4 text-left">Nom</th>
                <th class="px-1 py-4 text-left">Email</th>
                <th class="px-6 py-4 text-left">Rôle</th>
                <th class="px-6 py-4 text-left">Documents</th>
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
                    <td class="px-6 py-4 text-left">
        @if ($user->documents_verified)
            <span class="text-green-600">✅ Validés</span>
        @elseif ($user->identity_document && $user->driver_license)
            <div class="space-y-1">
                    <a href="{{ asset('storage/' . $user->identity_document) }}" target="_blank" class="text-indigo-600 underline block">Pièce d'identité</a>
                    <a href="{{ asset('storage/' . $user->driver_license) }}" target="_blank" class="text-indigo-600 underline block">Permis</a>


                <form action="{{ route('admin.validateDocuments', $user->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-blue-600 hover:underline mt-2">Valider</button>
                </form>
            </div>
        @else
            <span class="text-gray-500">Non envoyés</span>
        @endif

</td>

                    <td class="px-6 py-4 text-center">
                        <div class="space-y-1">
                            @if ($user->role !== 'admin')
                                <form action="{{ route('admin.promote', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button class="text-green-600 hover:underline">Promouvoir</button>
                                </form>
                            @endif

                            @if ($user->role !== 'client')
                                <form action="{{ route('admin.demote', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button class="text-yellow-600 hover:underline">Rétrograder</button>
                                </form>
                            @endif

                            <form action="{{ route('admin.delete', $user->id) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </x-admin-content>
</x-app-layout>
