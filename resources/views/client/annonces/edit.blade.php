<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Modifier l'annonce</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 space-y-8">

        <div>
            <a href="{{ route('client.annonces.index') }}" class="text-indigo-600 hover:underline text-sm">
                ← Retour à la liste des annonces
            </a>
        </div>

        {{-- Formulaire --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Modifier l'annonce</h3>

            <form method="POST" action="{{ route('client.annonces.update', $annonce) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Titre</label>
                    <x-text-input id="title" name="title" class="mt-1" value="{{ old('title', $annonce->title) }}" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <x-textarea id="description" name="description" rows="4" class="mt-1":value="old('description', $annonce->description)" }}/>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <div>
                    <label for="preferred_date" class="block text-sm font-medium text-gray-700">Date souhaitée</label>
                    <x-text-input type="date" id="preferred_date" name="preferred_date" class="mt-1" value="{{ old('preferred_date', $annonce->preferred_date) }}" />
                    <x-input-error :messages="$errors->get('preferred_date')" class="mt-1" />
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                    <select name="type" id="type" onchange="toggleFields()" class="mt-1 block w-full rounded border-gray-300">
                        <option value="transport" {{ old('type', $annonce->type) == 'transport' ? 'selected' : '' }}>Transport</option>
                        <option value="service" {{ old('type', $annonce->type) == 'service' ? 'selected' : '' }}>Service</option>
                    </select>
                    <x-input-error :messages="$errors->get('type')" class="mt-1" />
                </div>

                <div id="transport-fields" class="space-y-4" style="{{ old('type', $annonce->type) === 'transport' ? '' : 'display: none;' }}">
                    <div>
                        <label for="from_city" class="block text-sm font-medium text-gray-700">Ville de départ</label>
                        <x-text-input id="from_city" name="from_city" class="mt-1" value="{{ old('from_city', $annonce->from_city) }}" autocomplete="off" />
                        <x-input-error :messages="$errors->get('from_city')" class="mt-1" />
                        <ul id="from_city_suggestions" class="absolute z-50 w-full bg-white border border-gray-200 rounded shadow hidden"></ul>
                        <input type="hidden" name="from_lat" id="from_lat" value="{{ old('from_lat') }}">
                        <input type="hidden" name="from_lng" id="from_lng" value="{{ old('from_lng') }}">
                    </div>

                    <div>
                        <label for="to_city" class="block text-sm font-medium text-gray-700">Ville d’arrivée</label>
                        <x-text-input id="to_city" name="to_city" class="mt-1" value="{{ old('to_city', $annonce->to_city) }}" autocomplete="off" />
                        <x-input-error :messages="$errors->get('to_city')" class="mt-1" />
                        <ul id="to_city_suggestions" class="absolute z-50 w-full bg-white border border-gray-200 rounded shadow hidden"></ul>
                        <input type="hidden" name="to_lat" id="to_lat" value="{{ old('to_lat') }}">
                        <input type="hidden" name="to_lng" id="to_lng" value="{{ old('to_lng') }}">
                    </div>

                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700">Poids (kg)</label>
                        <x-text-input id="weight" name="weight" type="number" step="0.01" class="mt-1" value="{{ old('weight', $annonce->weight) }}" />
                        <x-input-error :messages="$errors->get('weight')" class="mt-1" />
                    </div>

                    <div>
                        <label for="volume" class="block text-sm font-medium text-gray-700">Volume (m³)</label>
                        <x-text-input id="volume" name="volume" type="number" step="0.01" class="mt-1" value="{{ old('volume', $annonce->volume) }}" />
                        <x-input-error :messages="$errors->get('volume')" class="mt-1" />
                    </div>
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Prix (€)</label>
                    <x-text-input id="price" name="price" type="number" step="0.01" class="mt-1" value="{{ old('price', $annonce->price) }}" />
                    <x-input-error :messages="$errors->get('price')" class="mt-1" />
                </div>

                <div>
                    <label for="constraints" class="block text-sm font-medium text-gray-700">Contraintes</label>
                    <x-textarea id="constraints" name="constraints" rows="2" class="mt-1":value="old('constraints', $annonce->constraints)"/>
                    <x-input-error :messages="$errors->get('constraints')" class="mt-1" />
                </div>

                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700">Photo</label>
                    @if ($annonce->photo)
                        <img src="{{ asset('storage/' . $annonce->photo) }}" alt="Photo actuelle" class="w-24 h-24 object-cover mb-2 rounded">
                    @endif
                    <input type="file" name="photo" id="photo" class="mt-1 block w-full border border-gray-300 rounded" />
                    <x-input-error :messages="$errors->get('photo')" class="mt-1" />
                </div>

                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Enregistrer les modifications
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
