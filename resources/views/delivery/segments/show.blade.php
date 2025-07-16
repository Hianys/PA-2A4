<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">
            Détail du segment de livraison
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 space-y-6">

        {{-- Lien retour --}}
        <div>
            <a href="{{ route('delivery.segments.index') }}"
               class="text-indigo-600 hover:underline text-sm">
                ← Retour à mes livraisons
            </a>
        </div>

        {{-- Détails du segment --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-bold mb-4">
                Informations sur le segment
            </h3>

            <p class="mb-2"><strong>De :</strong> {{ $segment->from_city }}</p>
            <p class="mb-2"><strong>Vers :</strong> {{ $segment->to_city }}</p>

            @if ($segment->annonce)
                <p class="mb-2">
                    <strong>Annonce liée :</strong>
                    <a href="{{ route('delivery.annonces.show', $segment->annonce_id) }}"
                       class="text-indigo-600 hover:underline">
                        {{ $segment->annonce->title }}
                    </a>
                </p>
                <p class="mb-2">
                    <strong>Date souhaitée :</strong>
                    {{ \Carbon\Carbon::parse($segment->annonce->preferred_date)->format('d/m/Y') }}
                </p>
            @else
                <p class="text-red-600">L'annonce liée a été supprimée.</p>
            @endif

            <p class="mb-2"><strong>Statut :</strong> {{ ucfirst($segment->status) }}</p>

            @if ($segment->price)
                <p class="mb-2"><strong>Prix :</strong> {{ $segment->price }} €</p>
            @endif

            @if ($segment->weight)
                <p class="mb-2"><strong>Poids :</strong> {{ $segment->weight }} kg</p>
            @endif

            @if ($segment->volume)
                <p class="mb-2"><strong>Volume :</strong> {{ $segment->volume }} m³</p>
            @endif

            @if ($segment->constraints)
                <p class="mb-2"><strong>Contraintes :</strong> {{ $segment->constraints }}</p>
            @endif

        </div>

        {{-- Actions possibles selon statut --}}
        @if ($segment->status === 'accepté')
            <form action="{{ route('segments.updateStatus', $segment) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="en cours">
                <button type="submit"
                        class="mt-4 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">
                    Commencer la livraison
                </button>
            </form>
        @elseif ($segment->status === 'en cours')
            <form action="{{ route('segments.updateStatus', $segment) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="livré">
                <button type="submit"
                        class="mt-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                    Marquer comme livré
                </button>
            </form>
        @elseif ($segment->status === 'livré')
            <div class="mt-4 text-green-700 font-bold">
                Cette livraison est terminée.
            </div>
        @endif

    </div>
</x-app-layout>
