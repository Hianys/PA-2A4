<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">@lang('Detail of the service announcement')</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 space-y-6">

        {{-- Lien retour --}}
        <div class="flex justify-between">
            <a href="{{ route('provider.dashboard') }}" class="text-indigo-600 hover:underline text-sm">
                ← @lang("Back to dashboard")
            </a>
            <a href="{{ route('provider.annonces.missions') }}" class="text-indigo-600 hover:underline text-sm">
                @lang("My accepted missions") →
            </a>
        </div>

    </div>

        {{-- Détails de l’annonce --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-bold">{{ $annonce->title }}</h3>
            <p class="text-sm text-gray-600 mt-2">{{ $annonce->description }}</p>

            <div class="mt-4 text-sm text-gray-700 space-y-1">
                <p><strong>@lang('Preferred date'):</strong> {{ \Carbon\Carbon::parse($annonce->preferred_date)->format('d/m/Y') }}</p>
                <p><strong>@lang('Price'):</strong> {{ $annonce->price }} €</p>
                <p><strong>@lang('Constraints'):</strong> {{ $annonce->constraints }}</p>
                <p><strong>@lang('Status'):</strong> {{ ucfirst($annonce->status) }}</p>
            </div>
        </div>

        {{-- Boutons d'action --}}
        <div class="bg-white shadow rounded-lg p-6">
            @if (session('success'))
                <div class="text-green-600 text-sm mb-3">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="text-red-600 text-sm mb-3">{{ session('error') }}</div>
            @endif   

            {{-- Affiche le bouton pour accepter l’annonce si elle est encore disponible --}}
            @if ($annonce->status === 'publiée')
                <form action="{{ route('provider.annonces.accept', $annonce) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        @lang('Accept this mission')
                    </button>
                </form>

            {{-- Affiche le bouton pour marquer comme réalisée si elle est déjà acceptée par ce prestataire --}}
            @elseif ($annonce->status === 'prise en charge' && $annonce->provider_id === auth()->id())
                <form action="{{ route('provider.annonces.complete', $annonce) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        @lang('Mark as completed')
                    </button>
                </form>
            @endif
        </div>

    </div>
</x-app-layout>