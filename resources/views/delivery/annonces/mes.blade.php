<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Mes annonces prises en charge</h2>
    </x-slot>

    <div class="max-w-5xl mx-auto py-6 space-y-6">
        @if ($annonces->count())
            @foreach ($annonces as $annonce)
                <div class="bg-white shadow rounded-lg p-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="space-y-1 w-full md:w-4/5">
                        <h3 class="text-lg font-bold text-indigo-700">{{ $annonce->title }}</h3>
                        <p class="text-sm text-gray-600 italic">{{ $annonce->description }}</p>
                        <div class="text-sm text-gray-700">
                            <p><strong>Trajet :</strong> {{ $annonce->from_city }} → {{ $annonce->to_city }}</p>
                            <p><strong>Date :</strong> {{ \Carbon\Carbon::parse($annonce->preferred_date)->format('d/m/Y') }}</p>
                            <p><strong>Statut :</strong> {{ $annonce->status }}</p>
                        </div>
                    </div>

                    <div class="md:w-1/5 text-right space-y-2">
                        <a href="{{ route('delivery.annonces.show', $annonce) }}"
                           class="inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                            Voir
                        </a>

                        @if ($annonce->status === 'prise en charge' && $annonce->livreur_id === auth()->id())
                            <form action="{{ route('delivery.annonces.markPending', $annonce) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded">
                                    Marquer en attente de paiement
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-center text-gray-600">Aucune annonce prise en charge.</p>
        @endif
    </div>
</x-app-layout>