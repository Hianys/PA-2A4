<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
            Bonjour, {{ Auth::user()->name }} 👋
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Colis à livrer</h3>
                <a href="{{ route('delivery.annonces.index') }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Accéder aux livraisons
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Trajets disponibles</h3>
                <p class="text-gray-600 dark:text-gray-300">Consultez les trajets proposés par les commerçants.</p>
            </div>

            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Historique de livraisons</h3>
                <p class="text-gray-600 dark:text-gray-300">Votre historique de livraison s'affichera ici.</p>
            </div>

        </div>
    </div>
</x-app-layout>
