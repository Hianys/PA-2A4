<x-app-layout>
    <div class="max-w-xl mx-auto py-16 text-center">
        <h1 class="text-3xl font-bold text-green-600 mb-4"> Paiement réussi</h1>
        <p class="text-gray-700 mb-6">Votre portefeuille a bien été rechargé. Merci !</p>

        <a href="{{ route('wallet.index') }}" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded hover:bg-indigo-700">
            Retour au portefeuille
        </a>
    </div>
</x-app-layout>