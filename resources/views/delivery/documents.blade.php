<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Vérification des documents</h2>
    </x-slot>

    <div class="max-w-xl mx-auto py-10 space-y-8">

        @if (session('success'))
            <div class="bg-green-100 text-green-800 border border-green-300 p-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 text-red-800 border border-red-300 p-4 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-800 border border-red-300 p-4 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (auth()->user()->identity_document && auth()->user()->driver_license)
            <div class="bg-white p-6 shadow rounded space-y-4">
                <p class="text-gray-700 font-medium">Vous avez déjà soumis vos documents.</p>

                <div class="space-y-2 text-sm text-gray-600">
                    <p>Pièce d'identité : <a href="{{ asset('storage/' . auth()->user()->identity_document) }}" target="_blank" class="text-indigo-600 underline">Voir</a></p>
                    <p>Permis de conduire : <a href="{{ asset('storage/' . auth()->user()->driver_license) }}" target="_blank" class="text-indigo-600 underline">Voir</a></p>
                </div>

                <div class="mt-4 p-3 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded text-sm">
                    Vos documents sont en attente de validation. Vous ne pouvez pas les modifier pour le moment.
                </div>
            </div>
        @else
            <form action="{{ route('delivery.documents.upload') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 shadow rounded space-y-6">
                @csrf

                <div>
                    <label for="identity_document" class="block text-sm font-medium text-gray-700 mb-1">
                        Pièce d'identité (PDF, JPG, PNG)
                    </label>
                    <input type="file" name="identity_document" accept=".pdf,.jpg,.jpeg,.png" required
                           class="block w-full mt-1 border-gray-300 rounded shadow-sm">
                </div>

                <div>
                    <label for="driver_license" class="block text-sm font-medium text-gray-700 mb-1">
                        Permis de conduire (PDF, JPG, PNG)
                    </label>
                    <input type="file" name="driver_license" accept=".pdf,.jpg,.jpeg,.png" required
                           class="block w-full mt-1 border-gray-300 rounded shadow-sm">
                </div>

                <div class="text-center">
                    <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-semibold shadow">
                        Envoyer les documents
                    </button>
                </div>
            </form>
        @endif
    </div>
</x-app-layout>
