<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Détail de l'annonce à livrer</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 space-y-6">

        {{-- Lien retour --}}
        <div>
            <a href="{{ route('delivery.annonces.index') }}" class="text-indigo-600 hover:underline text-sm">
                ← Retour à la liste des annonces
            </a>
        </div>

        {{-- Détails de l’annonce --}}
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

        {{-- Carte interactive --}}
        <x-leaflet-annonce-map :annonce="$annonce" :segments="$segments" />


        {{-- Segments pris en charge --}}
        @if ($annonce->type === 'transport')
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-md font-semibold mb-2">Segments déjà pris en charge</h3>

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

        {{-- Formulaire pour proposer un segment --}}
        @if ($annonce->type === 'transport')
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-md font-semibold mb-4">Proposer un segment à prendre en charge</h3>

                @if (session('success'))
                    <div class="text-green-600 text-sm mb-3">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="text-red-600 text-sm mb-3">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('segments.store', $annonce) }}" class="space-y-4 relative">
                    @csrf

                    <div class="relative">
                        <label for="from_city" class="block text-sm font-medium text-gray-700">Ville de départ</label>
                        <x-text-input id="from_city" name="from_city" autocomplete="off" class="mt-1" required />
                        <ul id="from_city_suggestions" class="absolute z-50 w-full bg-white border border-gray-200 rounded shadow hidden"></ul>
                        <x-input-error :messages="$errors->get('from_city')" class="mt-1" />
                    </div>

                    <div class="relative">
                        <label for="to_city" class="block text-sm font-medium text-gray-700">Ville d’arrivée</label>
                        <x-text-input id="to_city" name="to_city" autocomplete="off" class="mt-1" required />
                        <ul id="to_city_suggestions" class="absolute z-50 w-full bg-white border border-gray-200 rounded shadow hidden"></ul>
                        <x-input-error :messages="$errors->get('to_city')" class="mt-1" />
                    </div>

                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        Valider ce segment
                    </button>
                </form>
            </div>
        @endif
    </div>
    <x-autocomplete-script />
</x-app-layout>
