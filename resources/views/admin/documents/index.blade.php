<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
            Validation des documents
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-8 space-y-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <p class="text-lg font-medium mb-4 text-gray-700 dark:text-gray-200">Choisissez une catégorie :</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <a href="{{ route('admin.delivery.documents') }}"
                   class="block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-4 px-6 rounded-lg text-center shadow transition">
                    Documents des Livreurs
                </a>

                <a href="{{ route('admin.trader.documents') }}"
                   class="block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-4 px-6 rounded-lg text-center shadow transition">
                    Documents des Commerçants
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
