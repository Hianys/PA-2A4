<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Mes livraisons</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 space-y-6">
        {{-- Segments en attente de validation --}}
        @if ($segments->where('status', 'en attente')->count())
            <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 p-4 mb-6">
                <p class="font-semibold mb-2">En attente de validation :</p>

                @foreach ($segments->where('status', 'en attente') as $segment)
                    <div class="border border-yellow-300 rounded p-3 mb-3 bg-yellow-100">
                        <p class="text-sm text-gray-700">
                            Annonce :
                            @if ($segment->annonce)
                                <a href="{{ route('delivery.annonces.show', $segment->annonce_id) }}"
                                   class="text-indigo-600 hover:underline">
                                    {{ $segment->annonce->title }}
                                </a>
                            @else
                                Annonce supprimée
                            @endif
                        </p>
                        <p class="font-semibold">
                            <a href="{{ route('segments.show', $segment) }}"
                               class="text-indigo-600 hover:underline">
                                {{ $segment->from_city }} → {{ $segment->to_city }}
                            </a>
                        </p>
                        @if ($segment->annonce && $segment->annonce->preferred_date)
                            <p class="text-sm text-gray-600 mt-1">
                                Date souhaitée :
                                {{ \Carbon\Carbon::parse($segment->annonce->preferred_date)->format('d/m/Y') }}
                            </p>
                        @endif
                        <p class="text-sm text-yellow-800 mt-1 font-medium">
                            Statut : En attente de validation par le client
                        </p>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Segments en cours ou livrés --}}
        @if ($segments->whereIn('status', ['accepté', 'en cours', 'livré'])->count())
            <div class="space-y-4">
                @foreach ($segments->whereIn('status', ['accepté', 'en cours', 'livré']) as $segment)
                    <div class="border p-4 rounded shadow bg-white">
                        <div class="flex justify-between">
                            <div>
                                <p class="text-sm text-gray-500">
                                    Annonce :
                                    @if ($segment->annonce)
                                        <a href="{{ route('delivery.annonces.show', $segment->annonce_id) }}"
                                           class="text-indigo-600 hover:underline">
                                            {{ $segment->annonce->title }}
                                        </a>
                                    @else
                                        Annonce supprimée
                                    @endif
                                </p>
                                <p class="font-semibold text-lg">
                                    <a href="{{ route('segments.show', $segment) }}"
                                       class="text-indigo-600 hover:underline">
                                        {{ $segment->from_city }} → {{ $segment->to_city }}
                                    </a>
                                </p>
                                @if ($segment->annonce && $segment->annonce->preferred_date)
                                    <p class="text-sm text-gray-600 mt-1">
                                        Statut :
                                        <span class="font-semibold">{{ ucfirst($segment->status) }}</span>
                                        | Date souhaitée :
                                        {{ \Carbon\Carbon::parse($segment->annonce->preferred_date)->format('d/m/Y') }}
                                    </p>
                                @endif
                            </div>

                            <div class="self-center space-x-2">
                                @if ($segment->status === 'accepté')
                                    <form action="{{ route('segments.updateStatus', $segment) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="en cours">
                                        <button type="submit" class="text-xs bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">
                                            Démarrer
                                        </button>
                                    </form>
                                @elseif ($segment->status === 'en cours')
                                    <form action="{{ route('segments.updateStatus', $segment) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="livré">
                                        <button type="submit" class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">
                                            Marquer comme livré
                                        </button>
                                    </form>
                                @elseif ($segment->status === 'livré')
                                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">Livré</span>
                                @else
                                    <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                        {{ ucfirst($segment->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500">Aucune livraison en cours.</p>
        @endif
    </div>
</x-app-layout>
