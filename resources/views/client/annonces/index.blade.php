<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Mes annonces</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto py-6 space-y-10">
        @if (session('success'))
            <div class="bg-green-100 text-green-700 border border-green-300 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Formulaire de création --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Nouvelle annonce</h3>
                <form action="{{ route('client.annonces.store') }}" method="POST" class="bg-white p-6 shadow rounded space-y-4 mb-8">
            @csrf

            <div>
                <label class="block font-medium">Titre</label>
                <input type="text" name="title" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block font-medium">Description</label>
                <textarea name="description" class="w-full border rounded px-3 py-2"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium">Ville de départ</label>
                    <input type="text" name="from_city" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block font-medium">Ville d'arrivée</label>
                    <input type="text" name="to_city" class="w-full border rounded px-3 py-2" required>
                </div>
            </div>

            <div>
                <label class="block font-medium">Date souhaitée</label>
                <input type="date" name="preferred_date" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block font-medium">Type</label>
                <select name="type" class="w-full border rounded px-3 py-2" required>
                    <option value="transport">Transport</option>
                    <option value="service">Service</option>
                </select>
            </div>

            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Créer l'annonce
            </button>
        </form>
            </div>

        {{-- Liste des annonces --}}
        @if ($annonces->count())
                <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-3">Vos annonces</h3>
            <ul class="space-y-4">
                @foreach ($annonces as $annonce)
                    <li class="bg-white p-4 shadow rounded">
                        <div class="flex justify-between">
                            <div>
                                <h4 class="font-bold">{{ $annonce->title }} <span class="ml-2 text-xs px-2 py-1 rounded bg-gray-100 text-gray-700">{{ ucfirst($annonce->type) }}</span></h4>
                                <p class="text-sm text-gray-600">{{ $annonce->description }}</p>
                            </div>
                            <div class="text-right text-sm text-gray-500">
                                {{ $annonce->from_city }} → {{ $annonce->to_city }}<br>
                                le {{ \Carbon\Carbon::parse($annonce->preferred_date)->format('d/m/Y') }}
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
                </div>
        @else
            <p>Aucune annonce pour le moment.</p>
        @endif
    </div>
</x-app-layout>
