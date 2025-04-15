<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
            Bonjour, {{ Auth::user()->name }} 👋
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Prestations à venir</h3>
                <p class="text-gray-600 dark:text-gray-300">Aucune prestation programmée pour l'instant.</p>
            </div>

            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Calendrier</h3>
                <p class="text-gray-600 dark:text-gray-300">Votre planning apparaîtra ici.</p>
            </div>

            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Revenus</h3>
                <p class="text-gray-600 dark:text-gray-300">Vos revenus mensuels s'afficheront dans cette section.</p>
            </div>

        </div>
    </div>
</x-app-layout>
