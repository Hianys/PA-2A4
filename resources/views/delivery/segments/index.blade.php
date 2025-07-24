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
                            <a href="{{ route('delivery.annonces.show', $segment->annonce_id) }}"
                               class="text-indigo-600 hover:underline">
                                {{ $segment->annonce->title ?? 'Annonce supprimée' }}
                            </a>
                        </p>
                        <p class="font-semibold">
                            {{ $segment->from_city }} → {{ $segment->to_city }}
                        </p>
                        <p class="text-sm text-gray-600 mt-1">
                            Date souhaitée :
                            {{ \Carbon\Carbon::parse($segment->annonce->preferred_date)->format('d/m/Y') }}
                        </p>
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
                                    @lang('Announce') :
                                    <a href="{{ route('delivery.annonces.show', $segment->annonce_id) }}"
                                       class="text-indigo-600 hover:underline">
                                        {{ $segment->annonce ? $segment->annonce->title : __('deleted announcement') }}
                                    </a>
                                </p>
                                <p class="font-semibold text-lg">
                                    {{ $segment->from_city }} → {{ $segment->to_city }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    Statut :
                                    <span class="font-semibold">{{ ucfirst($segment->status) }}</span>
                                    @lang('Preferred date') : {{ \Carbon\Carbon::parse($segment->annonce->preferred_date)->format('d/m/Y') }}
                                </p>

                            </div>

                            <div class="self-center space-x-2">
                                @if ($segment->status === 'accepte')
                                    <form action="{{ route('segments.updateStatus', $segment) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="en cours">
                                        <button type="submit" class="text-xs bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">
                                            @lang('Start')
                                        </button>
                                    </form>
                                @elseif ($segment->status === 'en cours')
                                    <form action="{{ route('segments.updateStatus', $segment) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="livré">
                                        <button type="submit" class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">
                                            @lang('Mark as delivered')
                                        </button>
                                    </form>
                                @elseif ($segment->status === 'livre')
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
            <p class="text-center text-gray-500">@lang('No ongoing deliveries.')</p>
        @endif
    </div>
</x-app-layout>
