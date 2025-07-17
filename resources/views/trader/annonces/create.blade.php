<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">@lang("Create a new transport announcement")</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 space-y-8">

        {{-- Back link --}}
        <div>
            <a href="{{ route('commercant.annonces.index') }}" class="text-indigo-600 hover:underline text-sm">
                ← @lang("Back to announcements list")
            </a>
        </div>

        {{-- Form --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">@lang("New transport announcement")</h3>

            <form method="POST" action="{{ route('commercant.annonces.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">@lang("Title")</label>
                    <x-text-input id="title" name="title" class="mt-1" value="{{ old('title') }}" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <div>
                     <label for="description" class="block text-sm font-medium text-gray-700">@lang("Description")</label>
                    <textarea id="description" name="description" rows="4" class="mt-1 block w-full border border-gray-300 rounded">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <div>
                        <label for="from_city" class="block text-sm font-medium text-gray-700">Ville de départ</label>
                        <div class="relative">
                            <x-text-input id="from_city" name="from_city" class="mt-1" value="{{ old('from_city') }}" autocomplete="off" />
                            <x-input-error :messages="$errors->get('from_city')" class="mt-1" />
                            <ul id="from_city_suggestions" class="absolute z-50 w-full bg-white border border-gray-200 rounded shadow hidden"></ul>
                        </div>
                        <input type="hidden" name="from_lat" id="from_lat" value="{{ old('from_lat') }}">
                        <input type="hidden" name="from_lng" id="from_lng" value="{{ old('from_lng') }}">

                    </div>

                <div>
                        <label for="to_city" class="block text-sm font-medium text-gray-700">Ville d’arrivée</label>
                        <div class="relative">
                            <x-text-input id="to_city" name="to_city" class="mt-1" value="{{ old('to_city') }}" autocomplete="off" />
                            <x-input-error :messages="$errors->get('to_city')" class="mt-1" />
                            <ul id="to_city_suggestions" class="absolute z-50 w-full bg-white border border-gray-200 rounded shadow hidden"></ul>
                        </div>
                        <input type="hidden" name="to_lat" id="to_lat" value="{{ old('to_lat') }}">
                        <input type="hidden" name="to_lng" id="to_lng" value="{{ old('to_lng') }}">

                    </div>

                <div>
                    <label for="preferred_date" class="block text-sm font-medium text-gray-700">@lang("Preferred date")</label>
                    <x-text-input type="date" id="preferred_date" name="preferred_date" class="mt-1" value="{{ old('preferred_date') }}" required />
                    <x-input-error :messages="$errors->get('preferred_date')" class="mt-1" />
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">@lang("Price") (€)</label>
                    <x-text-input id="price" name="price" type="number" step="0.01" class="mt-1" value="{{ old('price') }}" />
                    <x-input-error :messages="$errors->get('price')" class="mt-1" />
                </div>

                <div>
                    <label for="constraints" class="block text-sm font-medium text-gray-700">@lang("Constraints")</label>
                    <textarea id="constraints" name="constraints" rows="2" class="mt-1 block w-full border border-gray-300 rounded">{{ old('constraints') }}</textarea>
                    <x-input-error :messages="$errors->get('constraints')" class="mt-1" />
                </div>

                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700">@lang("Photo")</label>
                    <input type="file" name="photo" id="photo" class="mt-1 block w-full border border-gray-300 rounded" />
                    <x-input-error :messages="$errors->get('photo')" class="mt-1" />
                </div>

                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    @lang("Create announcement")
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