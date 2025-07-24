<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Validation des KBIS</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 space-y-4">
        @foreach ($users as $user)
            <div class="bg-white shadow p-4 rounded">
                <h3 class="font-semibold">{{ $user->name }}</h3>
                <p>Email : {{ $user->email }}</p>
                <p>KBIS :
                    @if ($user->kbis)
                        <a href="{{ asset('storage/' . $user->kbis) }}" target="_blank" class="text-blue-600 underline">Voir le fichier</a>
                    @else
                        <span class="text-red-500">Aucun fichier</span>
                    @endif
                </p>
                <p>Statut : {{ $user->kbis_valide ? 'Validé' : 'Non validé' }}</p>

                @if ($user->kbis)
                    <form method="POST" action="{{ route('admin.kbis.toggle', $user->id) }}" class="mt-2">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="{{ $user->kbis_valide ? 'bg-red-600' : 'bg-green-600' }} text-white px-4 py-2 rounded">
                            {{ $user->kbis_valide ? 'Annuler la validation' : 'Valider le KBIS' }}
                        </button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>
</x-app-layout>
