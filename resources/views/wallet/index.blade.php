<x-app-layout>
    <div class="max-w-4xl mx-auto py-10">
        <h1 class="text-3xl font-bold mb-8">Mon portefeuille</h1>

        <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="bg-white shadow rounded p-6">
                <p class="text-gray-600 text-sm">Solde disponible</p>
                <p class="text-2xl font-bold text-green-600">
                    {{ number_format($wallet->balance, 2) }} €
                </p>
            </div>
            <div class="bg-white shadow rounded p-6">
                <p class="text-gray-600 text-sm">Solde bloqué</p>
                <p class="text-2xl font-bold text-red-600">
                    {{ number_format($wallet->blocked_balance, 2) }} €
                </p>
            </div>
        </div>

        @if (auth()->user()->role === 'client' || auth()->user()->role === 'commercant')
            <div class="bg-white shadow rounded p-6 mb-10">
                <h2 class="text-xl font-semibold mb-4">Recharger mon portefeuille</h2>
                <form method="POST" action="{{ route('wallet.checkout') }}" class="flex space-x-4">
                    @csrf
                    <input type="number" name="amount" placeholder="Montant en €" min="1" step="1" required
                        class="border border-gray-300 rounded p-2 w-48">
                    <button type="submit"
                        class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        Recharger
                    </button>
                </form>
            </div>
        @elseif(auth()->user()->role === 'livreur' || auth()->user()->role === 'prestataire')
            <div class="bg-white shadow rounded p-6 mb-10">
                <h2 class="text-xl font-semibold mb-4">Retirer mes fonds</h2>
                <form method="POST" action="{{ route('wallet.withdraw') }}" class="flex space-x-4">
                    @csrf
                    <input type="number" name="amount" placeholder="Montant en €" min="1" step="1" required
                        class="border border-gray-300 rounded p-2 w-48">
                    <button type="submit"
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Retirer
                    </button>
                </form>
            </div>
        @endif

        <div class="bg-white shadow rounded p-6">
            <h2 class="text-xl font-semibold mb-4">Historique des transactions</h2>

            @if ($wallet->transactions->count())
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">Type</th>
                            <th class="px-4 py-2">Montant</th>
                            <th class="px-4 py-2">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($wallet->transactions as $transaction)
                            <tr>
                                <td class="px-4 py-2 text-gray-500">
                                    {{ $transaction->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-2">
                                    @if ($transaction->type === 'recharge')
                                        <span class="text-green-600 font-semibold">Recharge</span>
                                    @elseif ($transaction->type === 'delivery')
                                        <span class="text-red-600 font-semibold">Livraison</span>
                                    @else
                                        {{ ucfirst($transaction->type) }}
                                    @endif
                                </td>
                                <td class="px-4 py-2 font-semibold
                                    {{ $transaction->type === 'recharge' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($transaction->amount, 2) }} €
                                </td>
                                <td class="px-4 py-2">
                                    <span class="text-gray-600">{{ ucfirst($transaction->status) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500">Aucune transaction enregistrée.</p>
            @endif
        </div>
    </div>
</x-app-layout>