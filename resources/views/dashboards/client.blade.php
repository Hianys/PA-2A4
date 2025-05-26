<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
            Bienvenue, {{ Auth::user()->name }} 👋
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-4 rounded shadow">
                <h3 class="text-lg font-semibold mb-2">Mes annonces</h3>
                <p class="text-sm text-gray-600 mb-4">Créez, gérez et suivez vos demandes de transport ou de services.</p>
                <a href="{{ route('client.annonces.index') }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Accéder à mes annonces
                </a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Mes prestations</h3>
                <p class="text-gray-600">Aucune prestation réservée pour le moment.</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Historique</h3>
                <p class="text-gray-600">Votre historique de commandes apparaîtra ici.</p>
            </div>

        </div>
    </div>
</x-app-layout>
