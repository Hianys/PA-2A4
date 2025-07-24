<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Validation des documents des livreurs
        </h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="bg-green-100 text-green-800 border border-green-300 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($livreurs->isEmpty())
            <p class="text-gray-600">Aucun livreur en attente de validation de documents.</p>
        @else
            <table class="w-full bg-white shadow-md rounded overflow-hidden">
                <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left">Nom</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-left">Pièce d'identité</th>
                    <th class="px-4 py-2 text-left">Permis de conduire</th>
                    <th class="px-4 py-2 text-left">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($livreurs as $livreur)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $livreur->name }}</td>
                        <td class="px-4 py-2">{{ $livreur->email }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ asset('storage/' . $livreur->identity_document) }}" target="_blank" class="text-indigo-600 hover:underline">
                                Voir pièce d'identité
                            </a>
                        </td>
                        <td class="px-4 py-2">
                            <a href="{{ asset('storage/' . $livreur->driver_license) }}" target="_blank" class="text-indigo-600 hover:underline">
                                Voir permis
                            </a>
                        </td>
                        <td class="px-4 py-2">
                            <form action="{{ route('livreurs.documents.validate', $livreur->id) }}" method="POST" onsubmit="return confirm('Valider ces documents ?');">
                                @csrf
                                <button type="submit" class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700">
                                    Valider
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-app-layout>
