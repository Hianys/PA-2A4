<x-app-layout>
    <x-slot name="header">
        <h2>Mon profil commerçant</h2>
    </x-slot>

    <div class="max-w-xl mx-auto py-6">
        @if(session('success'))
            <div class="bg-green-200 text-green-800 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('commercant.profile.update') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label>Enseigne</label>
                {{-- old en cas d'erreur prend la premiere valeur, sinon la 2emeuh --}}
                <input type="text" name="enseigne" value="{{ old('enseigne', auth()->user()->enseigne) }}" class="block w-full rounded p-2 border" />
            </div>

            <div class="mb-4">
                <label>Adresse</label>
                <input type="text" name="adresse" value="{{ old('adresse', auth()->user()->adresse) }}" class="block w-full rounded p-2 border" />
            </div>

            <div class="mb-4">
                <label for="kbis">Kbis (PDF/JPG/PNG)</label>
                <input type="file" name="kbis" class="block w-full rounded p-2 border" accept=".jpg,.jpeg,.png,.pdf" id="kbis">
            </div>

            <button class="bg-indigo-600 text-white px-4 py-2 rounded">Mettre à jour</button>
        </form>

        @if(auth()->user()->kbis)
        {{-- asset construit l'url --}}
            <a href="{{ asset('storage/' . auth()->user()->kbis) }}" target="_blank" class="text-blue-600 underline block mt-2">
                Voir le document
            </a>
        @endif

        <a href="{{ route('commercant.consentement.form') }}" class="mt-6 inline-block bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600">
            Consentement de diffusion
        </a>

        @if(auth()->user()->consentement_valide)
            <div class="mt-2 p-3 bg-green-100 text-green-700 rounded">
                Consentement de diffusion déjà validé.
            </div>
        @endif
    </div>
</x-app-layout>