<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">
            @lang("Announcement details")
        </h2>
    </x-slot>

    {{-- grosse fonction pour calculer si un point rejoint bien un autreuh --}}

    @php
    function haversine($lat1, $lon1, $lat2, $lon2) {    
    if (!$lat1 || !$lon1 || !$lat2 || !$lon2) return 0;
    {{-- variable du ... rayon de la terre  --}}

    $earthRadius = 6371;
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);
    $deltaLat = $lat2 - $lat1;
    $deltaLon = $lon2 - $lon1;
    $a = sin($deltaLat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($deltaLon / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earthRadius * $c;
    }
    @endphp

    <div class="max-w-3xl mx-auto py-6">
        <div class="bg-white rounded shadow p-6 space-y-4">
            <h3 class="text-2xl font-bold">{{ $annonce->title }}</h3>
            <div class="text-gray-700 mb-2">{{ $annonce->description }}</div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <strong>@lang("Shipping from (your store)"):</strong>
                    <span>{{ $annonce->from_city }}</span>
                </div>
                <div>
                    <strong>@lang("Delivery address"):</strong>
                    <span>{{ $annonce->to_city }}</span>
                </div>
                <div>
                    <strong>@lang("Preferred date"):</strong>
                    <span>{{ \Carbon\Carbon::parse($annonce->preferred_date)->format('d/m/Y') }}</span>
                </div>
                <div>
                    <strong>@lang("Price"):</strong>
                    <span>{{ $annonce->price }} €</span>
                </div>
                <div>
                    <strong>@lang("Constraints"):</strong>
                    {{-- ?? = si n'est pas null alors affiche contrainte sinon affiche - --}}
                    <span>{{ $annonce->constraints ?? '-' }}</span>
                </div>
                <div>
                    <strong>@lang("Status"):</strong>
                    <span>{{ ucfirst($annonce->status) }}</span>
                </div>
            </div>
            
            @if($annonce->photo)
                <div class="mt-4">
                    <img src="{{ asset('storage/' . $annonce->photo) }}" alt="Photo" class="w-48 h-48 object-cover rounded">
                </div>
            @endif

            <div class="mt-4 flex gap-4">
                <a href="{{ route('commercant.annonces.edit', $annonce) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">@lang("Edit")</a>
                <form action="{{ route('commercant.annonces.destroy', $annonce) }}" method="POST" onsubmit="return confirm('@lang('Are you sure?')');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        @lang("Delete")
                    </button>
                </form>
            </div>
        </div>
        @if(auth()->user()->role === 'commercant')
    @if($annonce->livreur && !$annonce->is_paid)
        <div class="mt-4">
            <form method="POST" action="{{ route('commercant.annonces.payer', $annonce) }}">
                @csrf
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Payer {{ number_format($annonce->price, 2) }} €
                </button>
            </form>
        </div>
    @elseif($annonce->livreur && $annonce->is_paid && !$annonce->is_confirmed)
    <div class="mt-4">
        <form method="POST" action="{{ route('commercant.annonces.confirmer', $annonce) }}">
            @csrf
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Confirmer la livraison
            </button>
        </form>
    </div>
@endif

@if ($annonce->status === 'complétée')
    <div class="bg-white shadow rounded-lg p-6 mt-6">
        <h3 class="text-lg font-semibold mb-4">Récapitulatif des livreurs rémunérés</h3>

        {{-- récupère les segm et calcule leur distance totale puis vois qui fais quoi --}}

        @php
            $segments = $annonce->segments()->where('status', 'accepté')->get();
            $totalDistance = $segments->sum(function($s) {
                return haversine($s->from_lat, $s->from_lng, $s->to_lat, $s->to_lng);
            });
            $grouped = $segments->groupBy('delivery_id');
        @endphp

        {{-- création d'un tab--}}

        <table class="w-full text-sm text-left border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">Livreur</th>
                    <th class="p-2 border">Distance totale</th>
                    <th class="p-2 border">Part</th>
                    <th class="p-2 border">Montant reçu</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($grouped as $livreurId => $livreurSegments)
                    @php
                        $livreur = $livreurSegments->first()->delivery;
                        $livreurDistance = $livreurSegments->sum(function($s) {
                            return haversine($s->from_lat, $s->from_lng, $s->to_lat, $s->to_lng);
                        });
                        $part = $totalDistance > 0 ? $livreurDistance / $totalDistance : 0;
                        $montant = round($annonce->price * $part, 2);
                    @endphp
                    <tr class="border-t">
                        <td class="p-2 border">{{ $livreur->name }}</td>
                        <td class="p-2 border">{{ number_format($livreurDistance, 2) }} km</td>
                        <td class="p-2 border">{{ number_format($part * 100, 1) }} %</td>
                        <td class="p-2 border">{{ number_format($montant, 2) }} €</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@if($annonce->status === 'prise en charge')
    <div class="mt-4">
        <form method="POST" action="{{ route('commercant.annonces.complete', $annonce) }}">
            @csrf
            <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                Marquer comme complétée
            </button>
        </form>
    </div>
@endif
@endif

@if($annonce->status === 'complétée')
    <a href="{{ route('annonce.facture.pdf', $annonce) }}"
       class="inline-block bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800 mt-4">
        Télécharger la facture PDF
    </a>
@endif
{{-- Segments à valider --}}
@if ($annonce->segments->where('status', 'en attente')->count())
    <div class="mt-6 bg-white shadow rounded-lg p-6">
        <h3 class="text-md font-semibold mb-4">@lang("Supported Segments")</h3>

        @foreach ($annonce->segments->where('status', 'en attente') as $segment)
            <div class="border rounded p-4 mb-3">
                <p>{{ $segment->from_city }} → {{ $segment->to_city }} 
                    @if($segment->livreur)
                        <span class="text-sm text-gray-500">par {{ $segment->livreur->name }}</span>
                    @endif
                </p>

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
    
</x-app-layout>