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
                    <p><strong>@lang("Preferred date") :</strong> {{ \Carbon\Carbon::parse($annonce->preferred_date)->format('d/m/Y') }}</p>
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
            </div>
        </div>

        {{-- Carte --}}
        @if ($annonce->type === 'transport')
            <div id="map" class="w-full h-96 rounded shadow"></div>
        @endif

        {{-- Segments pris en charge --}}
        @if ($annonce->type === 'transport')
            <div class="bg-white shadow rounded-lg p-6 mt-6">
                <h3 class="text-md font-semibold mb-2">@lang("Supported Segments")</h3>

                @if ($segments->isEmpty())
                    <p class="text-gray-600">@lang("No segments have been supported yet.")</p>
                @else
                    <ul class="space-y-2">
                        @foreach ($segments as $segment)
                            <li class="border rounded p-2">
                                <p class="font-semibold">{{ $segment->from_city }} → {{ $segment->to_city }}</p>
                                <p class="text-sm text-gray-600">Par : {{ $segment->delivery->name ?? 'Inconnu' }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

    </div>

    {{-- Leaflet --}}
    <x-leaflet-annonce-map :annonce="$annonce" :segments="$segments" />

</x-app-layout>
