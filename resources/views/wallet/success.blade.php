<x-app-layout>
    <div class="max-w-xl mx-auto text-center mt-10">
        <h1 class="text-2xl font-bold text-green-600 mb-4">✅ Paiement réussi !</h1>
        <p class="text-lg">Votre portefeuille a bien été rechargé.</p>
        <a href="{{ route('wallet.index') }}" class="mt-6 inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
            Retour au portefeuille
        </a>
    </div>
</x-app-layout>
