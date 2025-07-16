<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold">Mes annonces</h2>
            <a href="{{ route('client.annonces.create') }}"
               class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Créer une annonce
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 space-y-8">

        {{-- Retour --}}
        <div>
            <a href="{{ route('client.dashboard') }}" class="text-indigo-600 hover:underline text-sm">
                ← Retour au tableau de bord
            </a>
        </div>

        {{-- Liste des annonces --}}
        @if ($annonces->count())
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Vos annonces</h3>
                <ul class="space-y-4">
                    @foreach ($annonces as $annonce)
                        <li class="border rounded p-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <a href="{{ route('client.annonces.show', $annonce) }}">
                                        <h4 class="font-bold">
                                            {{ $annonce->title }}
                                            <span class="ml-2 text-xs px-2 py-1 rounded bg-gray-200 text-gray-800">
                                                {{ ucfirst($annonce->type) }}
                                            </span>
                                        </h4>
                                    </a>
                                    <p class="text-sm text-gray-600 mt-1">{{ $annonce->description }}</p>

                                    <div class="text-sm text-gray-500 mt-2 space-y-1">
                                        <div><strong>Prix :</strong> {{ $annonce->price }} €</div>
                                        <div><strong>Poids :</strong> {{ $annonce->weight }} kg</div>
                                        <div><strong>Volume :</strong> {{ $annonce->volume }} m³</div>
                                        <div><strong>Contraintes :</strong> {{ $annonce->constraints }}</div>
                                        <div><strong>Statut :</strong> {{ $annonce->status }}</div>
                                    </div>
                                </div>
                                <div class="text-sm text-right text-gray-500">
                                    @if ($annonce->type === 'transport')
                                        {{ $annonce->from_city }} → {{ $annonce->to_city }}<br>
                                    @endif
                                    {{ $annonce->preferred_date ? \Carbon\Carbon::parse($annonce->preferred_date)->format('d/m/Y') : '' }}

                                    @if ($annonce->photo)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $annonce->photo) }}" alt="Photo" class="w-16 h-16 object-cover rounded">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <p class="text-center text-gray-500">Aucune annonce pour le moment.</p>
        @endif
    </div>

    {{-- Scripts --}}
    <x-autocomplete-script />

    <script>
        function toggleFields() {
            const type = document.getElementById('type').value;
            const transportFields = document.getElementById('transport-fields');
            transportFields.style.display = (type === 'transport') ? 'block' : 'none';
        }

        document.addEventListener('DOMContentLoaded', toggleFields);
    </script>
</x-app-layout>