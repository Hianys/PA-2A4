<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">@lang("Detail of the announcement")</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 space-y-6">

        {{-- Lien retour --}}
        <div>
            <a href="{{ route('client.annonces.index') }}" class="text-indigo-600 hover:underline text-sm">
                ← @lang("Back to announcements list")
            </a>
        </div>

        {{-- Détails de l'annonce --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-bold">{{ $annonce->title }}</h3>
            <p class="text-sm text-gray-600 mt-2">{{ $annonce->description }}</p>

            <div class="mt-4 text-sm text-gray-700 space-y-2">
                <p><strong>Type :</strong> {{ ucfirst($annonce->type) }}</p>
                <p><strong>Prix :</strong> {{ $annonce->price }} €</p>
                <p><strong>Poids :</strong> {{ $annonce->weight }} kg</p>
                <p><strong>Volume :</strong> {{ $annonce->volume }} m³</p>
                <p><strong>Contraintes :</strong> {{ $annonce->constraints }}</p>
                <p><strong>Statut :</strong> {{ $annonce->status }}</p>
            </div>

            @if ($annonce->type === 'transport')
                <div class="mt-4 text-sm text-gray-700">
                    <p><strong>@lang("From") :</strong> {{ $annonce->from_city }}</p>
                    <p><strong>@lang("To") :</strong> {{ $annonce->to_city }}</p>
                    <p><strong>@lang("Preferred date") :</strong>
                        {{ $annonce->preferred_date ? \Carbon\Carbon::parse($annonce->preferred_date)->format('d/m/Y') : 'Non précisée' }}
                    </p>
                </div>
            @else
                <div class="mt-4 text-sm text-gray-700">
                    <p><strong>Date souhaitée :</strong>
                        {{ $annonce->preferred_date ? \Carbon\Carbon::parse($annonce->preferred_date)->format('d/m/Y') : 'Non précisée' }}
                    </p>
                </div>
            @endif

            @if ($annonce->photo)
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-700 mb-2"><strong>Photo :</strong></p>
                    <img src="{{ asset('storage/' . $annonce->photo) }}"
                         alt="Photo de l'annonce"
                         class="w-full max-w-xs rounded shadow">
                </div>
            @endif

            <div class="mt-6 flex space-x-2">
                <a href="{{ route('client.annonces.edit', $annonce) }}"
                   class="bg-yellow-500 text-white px-3 py-2 rounded hover:bg-yellow-600">
                    Modifier
                </a>

                <form action="{{ route('client.annonces.destroy', $annonce) }}" method="POST"
                      onsubmit="return confirm('Confirmer la suppression ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">
                        Supprimer
                    </button>
                </form>

                <form action="{{ route('client.annonces.validate', $annonce) }}" method="POST"
                      onsubmit="return confirm('Confirmer la validation ?');">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">
                        Valider la livraison
                    </button>
                </form>
            </div>

            @if ($annonce->status === 'en attente de paiement')
                <div class="mt-6">
                    <form method="POST" action="{{ route('delivery.pay', $annonce->id) }}">
                        @csrf
                        <button
                            type="submit"
                            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                        >
                            Payer la livraison
                        </button>
                    </form>
                </div>
            @elseif ($annonce->status === 'bloqué')
                <div class="mt-6">
                    <form method="POST" action="{{ route('delivery.confirm', $annonce->id) }}">
                        @csrf
                        <button
                            type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                        >
                            Confirmer la livraison
                        </button>
                    </form>
                </div>
            @endif

        </div>

        {{-- Carte --}}
        @if ($annonce->type === 'transport')
            <div id="map" class="w-full h-96 rounded shadow"></div>
        @endif

        {{-- Segments pris en charge --}}
        @if ($segments->where('status', 'en attente')->count())
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-md font-semibold mb-4">@lang("Supported Segments")</h3>

                @foreach ($segments->where('status', 'en attente') as $segment)
                    <div class="border rounded p-4 mb-3">
                        <p>{{ $segment->from_city }} → {{ $segment->to_city }}</p>

                        <form method="POST" action="{{ route('segments.accept', $segment) }}" class="inline-block">
                            @csrf
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Accepter</button>
                        </form>

                        <form method="POST" action="{{ route('segments.refuse', $segment) }}" class="inline-block ml-2">
                            @csrf
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Refuser</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif


    </div>

    {{-- Leaflet --}}
    <x-leaflet-annonce-map :annonce="$annonce" :segments="$segments" />

</x-app-layout>
