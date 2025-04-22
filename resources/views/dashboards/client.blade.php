<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
            Bienvenue, {{ Auth::user()->name }} 👋
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Mes annonces</h3>
                <p class="text-gray-600">Vous n'avez pas encore publié d'annonces.</p>
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
