<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Toutes les annonces</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6">
        <div class="bg-white shadow rounded p-6">
            @if (count($annonces))
                <table class="w-full table-auto text-sm">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">Titre</th>
                        <th class="px-4 py-2">Type</th>
                        <th class="px-4 py-2">Auteur</th>
                        <th class="px-4 py-2">Statut</th>
                        <th class="px-4 py-2">Créé le</th>
                        <th class="px-4 py-2 text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($annonces as $annonce)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $annonce->id }}</td>
                            <td class="px-4 py-2">{{ $annonce->title }}</td>
                            <td class="px-4 py-2">{{ ucfirst($annonce->type) }}</td>
                            <td class="px-4 py-2">
                                {{ $annonce->user->name ?? 'N/A' }}
                                <span class="text-xs text-gray-500">
                                        ({{ ucfirst($annonce->user->role ?? 'N/A') }})
                                    </span>
                            </td>
                            <td class="px-4 py-2">{{ ucfirst($annonce->status) }}</td>
                            <td class="px-4 py-2">{{ $annonce->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 text-right">
                                <a href="{{ route('admin.annonces.show', $annonce->id) }}"
                                   class="text-indigo-600 hover:underline text-xs">
                                    Détails
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500">Aucune annonce enregistrée.</p>
            @endif
        </div>
    </div>
</x-app-layout>
