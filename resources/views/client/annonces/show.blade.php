<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Détail de l'annonce</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 space-y-8">

        {{-- Retour à la liste --}}
        <a href="{{ route('client.annonces.index') }}" class="text-sm text-indigo-600 hover:underline">
            ← Retour à mes annonces
        </a>

        {{-- Informations générales --}}
        <div class="bg-white shadow rounded-lg p-6 space-y-2">
            <h3 class="text-lg font-semibold text-indigo-700">{{ $annonce->title }}</h3>
            <p class="text-sm text-gray-600">{{ $annonce->description }}</p>
            <p><strong>Type :</strong> {{ ucfirst($annonce->type) }}</p>
            @if ($annonce->type === 'transport')
                <p><strong>Trajet :</strong> {{ $annonce->from_city }} → {{ $annonce->to_city }}</p>
                <p><strong>Date souhaitée :</strong> {{ \Carbon\Carbon::parse($annonce->preferred_date)->format('d/m/Y') }}</p>
            @endif
        </div>

        {{-- Formulaire de modification --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Modifier l'annonce</h3>
            <form action="{{ route('client.annonces.update', $annonce) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Titre</label>
                    <x-text-input name="title" id="title" value="{{ old('title', $annonce->title) }}" class="mt-1" required />
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <x-textarea name="description" id="description" rows="4" class="mt-1">{{ old('description', $annonce->description) }}</x-textarea>
                </div>

                @if ($annonce->type === 'transport')
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="from_city" class="block text-sm font-medium text-gray-700">Ville de départ</label>
                            <x-text-input name="from_city" id="from_city" value="{{ old('from_city', $annonce->from_city) }}" class="mt-1" />
                        </div>

                        <div>
                            <label for="to_city" class="block text-sm font-medium text-gray-700">Ville d’arrivée</label>
                            <x-text-input name="to_city" id="to_city" value="{{ old('to_city', $annonce->to_city) }}" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <label for="preferred_date" class="block text-sm font-medium text-gray-700">Date souhaitée</label>
                        <x-text-input type="date" name="preferred_date" id="preferred_date" value="{{ old('preferred_date', $annonce->preferred_date) }}" class="mt-1" />
                    </div>
                @endif

                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Enregistrer les modifications
                </button>
            </form>
        </div>

        {{-- Bouton de suppression --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-2">Supprimer l'annonce</h3>
            <form action="{{ route('client.annonces.destroy', $annonce) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:underline">Supprimer définitivement</button>
            </form>
        </div>
    </div>
</x-app-layout>
