<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Détail de l'annonce</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 space-y-6">

        {{-- Lien retour --}}
        <div>
            <a href="{{ route('client.annonces.index') }}" class="text-indigo-600 hover:underline text-sm">
                ← Retour à la liste des annonces
            </a>
        </div>

        {{-- Détails de l'annonce --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-bold">{{ $annonce->title }}</h3>
            <p class="text-sm text-gray-600 mt-2">{{ $annonce->description }}</p>

            @if ($annonce->type === 'transport')
                <div class="mt-4 text-sm text-gray-700">
                    <p><strong>De :</strong> {{ $annonce->from_city }}</p>
                    <p><strong>À :</strong> {{ $annonce->to_city }}</p>
                    <p><strong>Date souhaitée :</strong> {{ \Carbon\Carbon::parse($annonce->preferred_date)->format('d/m/Y') }}</p>
                </div>
            @endif
        </div>

        {{-- Carte --}}
        @if ($annonce->type === 'transport')
            <div id="map" class="w-full h-96 rounded shadow"></div>
        @endif

        {{-- Segments pris en charge --}}
        @if ($annonce->type === 'transport')
            <div class="bg-white shadow rounded-lg p-6 mt-6">
                <h3 class="text-md font-semibold mb-2">Segments pris en charge</h3>

                @if ($segments->isEmpty())
                    <p class="text-gray-600">Aucun segment n’a encore été pris en charge.</p>
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
