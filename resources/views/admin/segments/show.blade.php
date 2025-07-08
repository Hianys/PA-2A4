<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Détails du segment</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 space-y-6">

        <a href="{{ route('admin.annonces.show', $segment->annonce->id) }}"
           class="text-indigo-600 hover:underline text-sm">
            ← Retour à l’annonce
        </a>

        <div class="bg-white shadow p-6 rounded space-y-2">
            <p class="font-semibold text-lg">
                {{ $segment->from_city }} → {{ $segment->to_city }}
            </p>

            <p>
                <strong>Coordonnées départ :</strong>
                {{ $segment->from_lat ?? '-' }},
                {{ $segment->from_lng ?? '-' }}
            </p>

            <p>
                <strong>Coordonnées arrivée :</strong>
                {{ $segment->to_lat ?? '-' }},
                {{ $segment->to_lng ?? '-' }}
            </p>

            <p>
                <strong>Statut :</strong> {{ ucfirst($segment->status) }}
            </p>

            @if ($segment->delivery)
                <p>
                    <strong>Livreur :</strong>
                    <a href="{{ route('admin.users.show', $segment->delivery->id) }}"
                       class="text-indigo-600 hover:underline">
                        {{ $segment->delivery->name }} ({{ ucfirst($segment->delivery->role) }})
                    </a>
                </p>
            @else
                <p><strong>Livreur :</strong> N/A</p>
            @endif

            @if ($segment->annonce)
                <p>
                    <strong>Annonce liée :</strong>
                    <a href="{{ route('admin.annonces.show', $segment->annonce->id) }}"
                       class="text-indigo-600 hover:underline">
                        {{ $segment->annonce->title }}
                    </a>
                </p>
            @else
                <p><strong>Annonce :</strong> N/A</p>
            @endif
        </div>
        <div class="mt-4 flex space-x-2">
            <a href="{{ route('admin.segments.edit', $segment->id) }}"
               class="bg-indigo-600 text-white px-3 py-2 rounded hover:bg-indigo-700">
                Modifier
            </a>

            <form method="POST" action="{{ route('admin.segments.destroy', $segment->id) }}"
                  onsubmit="return confirm('Confirmer la suppression de ce segment ?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">
                    Supprimer
                </button>
            </form>
        </div>

    </div>
</x-app-layout>
