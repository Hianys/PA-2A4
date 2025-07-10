<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Mon portefeuille
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto bg-white shadow p-6 mt-8">
        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <p class="mb-4">Solde actuel : <strong>{{ number_format($wallet, 2) }} €</strong></p>

        <form method="POST" action="{{ route('wallet.checkout') }}">
            @csrf
            <label for="amount" class="block font-medium">Montant à recharger (€) :</label>
            <input type="number" name="amount" id="amount" class="border p-2 w-full my-2" min="1" step="1" required>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Recharger</button>
        </form>
    </div>
</x-app-layout>
