<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Annonce : {{ $annonce->title }}</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 space-y-8">
        <a href="{{ route('delivery.annonces.index') }}" class="text-sm text-indigo-600 hover:underline">
            ← Retour à la liste des annonces
        </a>

        {{-- Détails de l’annonce --}}
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-semibold mb-4">Détails de l’annonce</h3>
            <p><strong>Type :</strong> {{ ucfirst($annonce->type) }}</p>
            <p><strong>Description :</strong> {{ $annonce->description }}</p>
            <p><strong>Trajet :</strong> {{ $annonce->from_city }} → {{ $annonce->to_city }}</p>
            <p><strong>Date souhaitée :</strong> {{ \Carbon\Carbon::parse($annonce->preferred_date)->format('d/m/Y') }}</p>
        </div>

        {{-- Formulaire de prise en charge partielle --}}
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-semibold mb-4">Proposer une prise en charge partielle</h3>

            @if(session('success'))
                <p class="text-green-600 mb-3">{{ session('success') }}</p>
            @endif
            @if(session('error'))
                <p class="text-red-600 mb-3">{{ session('error') }}</p>
            @endif

            <form action="{{ route('segments.store', $annonce) }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="from_city" class="block text-sm font-medium text-gray-700">Ville de départ</label>
                        <input type="text" name="from_city" id="from_city" required class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label for="to_city" class="block text-sm font-medium text-gray-700">Ville d’arrivée</label>
                        <input type="text" name="to_city" id="to_city" required class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Valider la prise en charge
                </button>
            </form>
        </div>

        {{-- Liste des segments déjà pris --}}
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-semibold mb-4">Segments pris en charge</h3>

            @if ($annonce->segments->count())
                <ul class="space-y-2">
                    @foreach ($annonce->segments as $segment)
                        <li class="border rounded p-4">
                            <strong>{{ $segment->from_city }} → {{ $segment->to_city }}</strong><br>
                            Par : <span class="text-sm text-gray-700">{{ $segment->livreur->name }}</span><br>
                            Statut : <span class="text-sm text-gray-600">{{ $segment->status }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-600">Aucun segment pris en charge pour le moment.</p>
            @endif
        </div>

    </div>
</x-app-layout>
