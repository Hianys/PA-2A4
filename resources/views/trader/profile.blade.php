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

        <form method="POST" action="{{ route('commercant.profile.update') }}">
            @csrf
            @method('PATCH')
            
            <div class="mb-4">
                <label>Enseigne</label>
                <input type="text" name="enseigne" value="{{ old('enseigne', auth()->user()->enseigne) }}" required class="block w-full rounded p-2 border" />
            </div>

            <div class="mb-4">
                <label>Adresse</label>
                <input type="text" name="adresse" value="{{ old('adresse', auth()->user()->adresse) }}" required class="block w-full rounded p-2 border" />
            </div>

            <button class="bg-indigo-600 text-white px-4 py-2 rounded">Mettre à jour</button>
        </form>
    </div>
</x-app-layout>