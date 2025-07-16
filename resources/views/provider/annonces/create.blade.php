<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">@lang("Create a new service offer")</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 space-y-6">

        {{-- Back --}}
        <div>
            <a href="{{ route('provider.annonces.index') }}" class="text-indigo-600 hover:underline text-sm">
                ← @lang("Back to service list")
            </a>
        </div>

        {{-- Form --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">@lang("New service offer")</h3>

            <form method="POST" action="{{ route('provider.annonces.store') }}" enctype="multipart/form-data" class="space-y-4 relative">
                @csrf

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">@lang("Title")</label>
                    <x-text-input id="title" name="title" class="mt-1" value="{{ old('title') }}" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">@lang("Description")</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        class="mt-1 block w-full border border-gray-300 rounded"
                    >{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <input type="hidden" name="type" value="service" />

                <div>
                    <label for="preferred_date" class="block text-sm font-medium text-gray-700">@lang("Preferred date")</label>
                    <x-text-input type="date" id="preferred_date" name="preferred_date" class="mt-1" value="{{ old('preferred_date') }}" />
                    <x-input-error :messages="$errors->get('preferred_date')" class="mt-1" />
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">@lang("Price") (€)</label>
                    <x-text-input id="price" name="price" type="number" step="0.01" class="mt-1" value="{{ old('price') }}" />
                    <x-input-error :messages="$errors->get('price')" class="mt-1" />
                </div>

                <div>
                    <label for="constraints" class="block text-sm font-medium text-gray-700">@lang("Constraints")</label>
                    <textarea
                        id="constraints"
                        name="constraints"
                        rows="2"
                        class="mt-1 block w-full border border-gray-300 rounded"
                    >{{ old('constraints') }}</textarea>
                    <x-input-error :messages="$errors->get('constraints')" class="mt-1" />
                </div>

                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700">@lang("Photo")</label>
                    <input type="file" name="photo" id="photo" class="mt-1 block w-full border border-gray-300 rounded" />
                    <x-input-error :messages="$errors->get('photo')" class="mt-1" />
                </div>

                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    @lang("Create the offer")
                </button>
            </form>
        </div>
    </div>
</x-app-layout>