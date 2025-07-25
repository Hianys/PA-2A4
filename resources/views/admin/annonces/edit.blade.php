<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Modifier l’annonce</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 space-y-6">

        <form method="POST" action="{{ route('admin.annonces.update', $annonce->id) }}" enctype="multipart/form-data" class="space-y-4 relative">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700">Titre</label>
                <x-text-input name="title" value="{{ old('title', $annonce->title) }}" class="mt-1 w-full" required/>
                <x-input-error :messages="$errors->get('title')" class="mt-1" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" class="mt-1 w-full border-gray-300 rounded" rows="3">{{ old('description', $annonce->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-1" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Date souhaitée</label>
                <x-text-input type="date" name="preferred_date" value="{{ old('preferred_date', $annonce->preferred_date) }}" class="mt-1 w-full"/>
                <x-input-error :messages="$errors->get('preferred_date')" class="mt-1" />
            </div>

            <div id="transport-fields" class="{{ old('type', $annonce->type) == 'transport' ? '' : 'hidden' }} space-y-4">
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700">Ville de départ</label>
                    <x-text-input id="from_city" name="from_city" value="{{ old('from_city', $annonce->from_city) }}" class="mt-1 w-full" autocomplete="off"/>
                    <ul id="from_city_suggestions" class="absolute z-50 w-full bg-white border border-gray-200 rounded shadow hidden"></ul>
                    <x-input-error :messages="$errors->get('from_city')" class="mt-1" />
                </div>

                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700">Ville d’arrivée</label>
                    <x-text-input id="to_city" name="to_city" value="{{ old('to_city', $annonce->to_city) }}" class="mt-1 w-full" autocomplete="off"/>
                    <ul id="to_city_suggestions" class="absolute z-50 w-full bg-white border border-gray-200 rounded shadow hidden"></ul>
                    <x-input-error :messages="$errors->get('to_city')" class="mt-1" />
                </div>

                <input type="hidden" name="from_latitude" id="from_latitude" value="{{ old('from_latitude', $annonce->from_lat) }}">
                <input type="hidden" name="from_longitude" id="from_longitude" value="{{ old('from_longitude', $annonce->from_lng) }}">
                <input type="hidden" name="to_latitude" id="to_latitude" value="{{ old('to_latitude', $annonce->to_lat) }}">
                <input type="hidden" name="to_longitude" id="to_longitude" value="{{ old('to_longitude', $annonce->to_lng) }}">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Statut</label>
                <select name="status" class="mt-1 w-full border-gray-300 rounded">
                    @foreach(['publiée', 'prise en charge', 'complétée', 'archivée'] as $status)
                        <option value="{{ $status }}" {{ old('status', $annonce->status) == $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-1" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Photo</label>
                <input type="file" name="photo" class="mt-1 w-full border-gray-300 rounded">
                <x-input-error :messages="$errors->get('photo')" class="mt-1" />

                @if ($annonce->photo)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $annonce->photo) }}" alt="Photo" class="w-32 h-32 object-cover rounded">
                    </div>
                @endif
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('admin.annonces.show', $annonce->id) }}" class="text-gray-600 hover:underline">
                    Annuler
                </a>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Enregistrer
                </button>
            </div>
        </form>
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
