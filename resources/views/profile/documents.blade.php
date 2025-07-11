<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Vérification des documents</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto py-6 px-4 space-y-6">

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

        <form action="{{ route('livreur.documents.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="identity_document" class="block text-sm font-medium text-gray-700 mb-1">
                    Pièce d'identité (PDF, JPG, PNG)
                </label>
                <div class="flex items-center space-x-4">
                    <label class="bg-indigo-600 text-white px-4 py-2 rounded-md cursor-pointer hover:bg-indigo-700">
                        Choisir un fichier
                        <input type="file" name="identity_document" class="hidden" required onchange="document.getElementById('identity-filename').textContent = this.files[0]?.name || 'Aucun fichier choisi';">
                    </label>
                    <span id="identity-filename" class="text-sm text-gray-600">Aucun fichier choisi</span>
                </div>
            </div>

            <div>
                <label for="driver_license" class="block text-sm font-medium text-gray-700 mb-1">
                    Permis de conduire (PDF, JPG, PNG)
                </label>
                <div class="flex items-center space-x-4">
                    <label class="bg-indigo-600 text-white px-4 py-2 rounded-md cursor-pointer hover:bg-indigo-700">
                        Choisir un fichier
                        <input type="file" name="driver_license" class="hidden" required onchange="document.getElementById('license-filename').textContent = this.files[0]?.name || 'Aucun fichier choisi';">
                    </label>
                    <span id="license-filename" class="text-sm text-gray-600">Aucun fichier choisi</span>
                </div>
            </div>

            <div class="text-center">
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-semibold shadow">
                    Envoyer les documents
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
