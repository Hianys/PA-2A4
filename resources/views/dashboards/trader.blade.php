<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
            Bonjour, {{ Auth::user()->name }} 👋
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Commandes en cours</h3>
                <p class="text-gray-600 dark:text-gray-300">Aucune commande n'est active pour le moment.</p>
            </div>

            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Annonces actives</h3>
                <p class="text-gray-600 dark:text-gray-300">Gérez vos offres et demandes ici.</p>
            </div>

            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Facturation</h3>
                <p class="text-gray-600 dark:text-gray-300">Vos factures seront disponibles dans cet espace.</p>
            </div>

        </div>
    </div>
</x-app-layout>
