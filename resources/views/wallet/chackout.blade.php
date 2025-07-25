<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">
            Paiement sécurisé avec Stripe
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto py-8 px-6 bg-white shadow rounded space-y-6">

    <div class="text-center">
            <p class="text-gray-600 text-sm">Recharge du portefeuille</p>
            <p class="text-3xl font-bold text-indigo-600">
                {{ number_format($amount / 100, 2, ',', ' ') }} €
            </p>
        </div>

        <div class="bg-gray-100 p-4 rounded text-sm">
            <p class="font-semibold mb-2"> Paiement test — utilisez les infos suivantes :</p>
            <ul class="list-disc pl-5 space-y-1">
                <li><strong>Email :</strong> test@example.com</li>
                <li><strong>Numéro de carte :</strong> 4242 4242 4242 4242</li>
                <li><strong>Date :</strong> 12/34</li>
                <li><strong>CVC :</strong> 123</li>
                <li><strong>Nom :</strong> Jean Dupont </li>
            </ul>
        </div>


        <div class="text-center">
            <a href="{{ $checkoutUrl }}"
               class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded">
                Continuer vers le paiement sécurisé
            </a>
        </div>
    </div>
</x-app-layout>