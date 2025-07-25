<x-app-layout>
    <div class="max-w-xl mx-auto py-6">
        <h2 class="text-lg font-bold mb-4 text-center">Consentement de diffusion</h2>

        @if (auth()->user()->consentement_valide)
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-center">
                Vous avez déjà donné votre consentement.
            </div>
        @endif

        <form method="POST" action="{{ route('commercant.consentement.valider') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block font-medium mb-1">Nom de l'enseigne</label>
                <div class="p-2 bg-gray-100 border rounded">
                    {{ $enseigne }}
                </div>
                <input type="hidden" name="enseigne" value="{{ $enseigne }}">
            </div>

            {{-- petite box a cocher--}}

            <div class="flex items-center">
                <input type="checkbox" name="accept_terms" id="accept_terms"
                    class="mr-2"
                    {{ auth()->user()->consentement_valide ? 'checked' : '' }}>
                <label for="accept_terms">J'accepte les conditions de diffusion</label>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Valider ou retirer le consentement
            </button>
        </form>

        <form method="POST" action="{{ route('commercant.consentement.pdf') }}" class="mt-6">
            @csrf
            <x-secondary-button type="submit">Télécharger le PDF de consentement</x-secondary-button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('commercant.profile.edit') }}" class="inline-block bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600">
                Retour à mon profil
            </a>
        </div>
    </div>
</x-app-layout>