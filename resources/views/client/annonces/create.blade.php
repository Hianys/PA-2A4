<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Créer une nouvelle annonce</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 space-y-8">

        {{-- Retour --}}
        <div>
            <a href="{{ route('client.annonces.index') }}" class="text-indigo-600 hover:underline text-sm">
                ← Retour à la liste des annonces
            </a>
        </div>

        {{-- Formulaire --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Nouvelle annonce</h3>

            <form method="POST" action="{{ route('client.annonces.store') }}" enctype="multipart/form-data" class="space-y-4 relative">
                @csrf

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Titre</label>
                    <x-text-input id="title" name="title" class="mt-1" value="{{ old('title') }}" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        class="mt-1 block w-full border border-gray-300 rounded"
                    >{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <div>
                    <label for="preferred_date" class="block text-sm font-medium text-gray-700">Date souhaitée</label>
                    <x-text-input type="date" id="preferred_date" name="preferred_date" class="mt-1" value="{{ old('preferred_date') }}" />
                    <x-input-error :messages="$errors->get('preferred_date')" class="mt-1" />
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                    <select name="type" id="type" onchange="toggleFields()" class="mt-1 block w-full rounded border-gray-300">
                        <option value="transport" {{ old('type') == 'transport' ? 'selected' : '' }}>Transport</option>
                        <option value="service" {{ old('type') == 'service' ? 'selected' : '' }}>Service</option>
                    </select>
                    <x-input-error :messages="$errors->get('type')" class="mt-1" />
                </div>

                {{-- Champs spécifiques au transport --}}
                <div id="transport-fields" class="space-y-4" style="{{ old('type') === 'transport' ? '' : 'display: none;' }}">
                    <div>
                        <label for="from_city" class="block text-sm font-medium text-gray-700">Ville de départ</label>
                        <x-text-input id="from_city" name="from_city" class="mt-1" value="{{ old('from_city') }}" autocomplete="off" />
                        <x-input-error :messages="$errors->get('from_city')" class="mt-1" />
                    </div>

                    <div>
                        <label for="to_city" class="block text-sm font-medium text-gray-700">Ville d’arrivée</label>
                        <x-text-input id="to_city" name="to_city" class="mt-1" value="{{ old('to_city') }}" autocomplete="off" />
                        <x-input-error :messages="$errors->get('to_city')" class="mt-1" />
                    </div>

                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700">Poids (kg)</label>
                        <x-text-input id="weight" name="weight" type="number" step="0.01" class="mt-1" value="{{ old('weight') }}" />
                        <x-input-error :messages="$errors->get('weight')" class="mt-1" />
                    </div>

                    <div>
                        <label for="volume" class="block text-sm font-medium text-gray-700">Volume (m³)</label>
                        <x-text-input id="volume" name="volume" type="number" step="0.01" class="mt-1" value="{{ old('volume') }}" />
                        <x-input-error :messages="$errors->get('volume')" class="mt-1" />
                    </div>
                </div>

                {{-- Champs communs --}}
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Prix (€)</label>
                    <x-text-input id="price" name="price" type="number" step="0.01" class="mt-1" value="{{ old('price') }}" />
                    <x-input-error :messages="$errors->get('price')" class="mt-1" />
                </div>

                <div>
                    <label for="constraints" class="block text-sm font-medium text-gray-700">Contraintes</label>
                    <textarea
                        id="constraints"
                        name="constraints"
                        rows="2"
                        class="mt-1 block w-full border border-gray-300 rounded"
                    >{{ old('constraints') }}</textarea>
                    <x-input-error :messages="$errors->get('constraints')" class="mt-1" />
                </div>

                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700">Photo</label>
                    <input type="file" name="photo" id="photo" class="mt-1 block w-full border border-gray-300 rounded" />
                    <x-input-error :messages="$errors->get('photo')" class="mt-1" />
                </div>

                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Créer l'annonce
                </button>
            </form>
        </div>
    </div>

    <x-autocomplete-script />

    <script>
        function toggleFields() {
            const type = document.getElementById('type').value;
            const transportFields = document.getElementById('transport-fields');
            transportFields.style.display = (type === 'transport') ? 'block' : 'none';
        }

        document.addEventListener('DOMContentLoaded', toggleFields);
    </script>
</x-app-layout>
