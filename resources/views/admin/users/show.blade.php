<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Détails utilisateur</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 space-y-6">

        <div>
            <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:underline text-sm">
                ← Retour à la liste des utilisateurs
            </a>
        </div>

        <div class="bg-white shadow p-6 rounded">
            <h3 class="text-lg font-semibold mb-4">Informations</h3>
            <p><strong>Nom :</strong> {{ $user->name }}</p>
            <p><strong>Email :</strong> {{ $user->email }}</p>
            <p><strong>Rôle :</strong> {{ ucfirst($user->role) }}</p>
            <p><strong>Créé le :</strong> {{ $user->created_at->format('d/m/Y') }}</p>
        </div>

        <div class="bg-white shadow p-6 rounded flex gap-4">
            @if ($user->role !== 'admin')
                <form action="{{ route('admin.promote', $user->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                        Promouvoir
                    </button>
                </form>
            @endif

            @if ($user->role === 'admin')
                <form action="{{ route('admin.demote', $user->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">
                        Rétrograder
                    </button>
                </form>
            @endif
                <a href="{{ route('admin.users.edit', $user->id) }}"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                    Modifier
                </a>

                <form action="{{ route('admin.delete', $user->id) }}" method="POST"
                      onsubmit="return confirm('Confirmer la suppression ?')">
                    @csrf
                    @method('DELETE')
                    <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                        Supprimer
                    </button>
            </form>
        </div>

        @if (count($annonces))
            <div class="bg-white shadow p-6 rounded">
                <h3 class="text-lg font-semibold mb-4">Annonces</h3>
                <ul class="space-y-2">
                    @foreach ($annonces as $annonce)
                        <li class="border p-3 rounded flex justify-between">
                            <div>
                                <a href="{{ route('admin.annonces.show', $annonce->id) }}" class="text-indigo-600 hover:underline text-sm">
                                <p class="font-semibold">{{ $annonce->title }}</p>
                                </a>
                                <p class="text-sm text-gray-600">
                                    Type : {{ $annonce->type }} | Statut : {{ $annonce->status }}
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (count($segments))
            <div class="bg-white shadow p-6 rounded">
                <h3 class="text-lg font-semibold mb-4">Segments pris en charge</h3>
                <ul class="space-y-2">
                    @foreach ($segments as $segment)
                        <li class="border p-3 rounded flex justify-between">
                            <span>{{ $segment->from_city }} → {{ $segment->to_city }}</span>
                            <span class="text-sm text-gray-600">{{ ucfirst($segment->status) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

    </div>
</x-app-layout>
