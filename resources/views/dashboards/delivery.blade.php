<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
            Bonjour, {{ Auth::user()->name }} 👋
        </h2>
    </x-slot>

    {{-- Si l'utilisateur n'a pas encore envoyé ses documents --}}
@if (!auth()->user()->identity_document || !auth()->user()->driver_license)
    <div class="w-full bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 py-6 px-6 mb-6">
        <div class="max-w-4xl mx-auto text-center">
            <p class="font-semibold">⚠️ Vos documents ne sont pas encore validés.</p>
            <a href="{{ route('livreur.documents') }}" class="inline-block mt-4 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                Vérifier mes documents
            </a>
        </div>
    </div>
@endif

{{-- Si les documents ont été envoyés mais pas encore validés --}}
@if (
    !auth()->user()->documents_verified &&
    auth()->user()->identity_document &&
    auth()->user()->driver_license
)
    <div class="w-full bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 py-6 px-6 mb-6">
       <div class="max-w-4xl mx-auto text-center"> ⚠️ Vos documents sont en attente de validation.
        </div>
    </div>
@endif



    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Annonces des CLients</h3>
                <a href="{{ route('delivery.annonces.index') }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Accéder aux annonces
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Mes livraisons</h3>
                <a href="{{ route('delivery.segments.index') }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Accéder à mes trajets
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Historique de livraisons</h3>
                <p class="text-gray-600 dark:text-gray-300">Votre historique de livraison s'affichera ici.</p>
            </div>

        </div>
    </div>
</x-app-layout>
