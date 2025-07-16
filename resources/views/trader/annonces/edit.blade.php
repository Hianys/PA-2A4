<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">@lang("Edit the transport announcement")</h2>
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
            <h3 class="text-lg font-semibold mb-4">@lang("Edit announcement")</h3>

            <form method="POST" action="{{ route('commercant.annonces.update', $annonce) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">@lang("Title")</label>
                    <x-text-input id="title" name="title" class="mt-1" value="{{ old('title', $annonce->title) }}" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">@lang("Description")</label>
                    <textarea id="description" name="description" rows="4" class="mt-1 block w-full border border-gray-300 rounded">{{ old('description', $annonce->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                {{-- Adresse de départ (readonly) --}}
                <div>
                    <label for="from_city" class="block text-sm font-medium text-gray-700">@lang("Shipping from (your store)")</label>
                    <input type="text" name="from_city" id="from_city" value="{{ Auth::user()->adresse }}" readonly class="mt-1 block w-full border border-gray-300 rounded bg-gray-100" />
                </div>

                <div>
                    <label for="to_city" class="block text-sm font-medium text-gray-700">@lang("Delivery address (destination)")</label>
                    <x-text-input id="to_city" name="to_city" class="mt-1" value="{{ old('to_city', $annonce->to_city) }}" required />
                    <x-input-error :messages="$errors->get('to_city')" class="mt-1" />
                </div>

                <div>
                    <label for="preferred_date" class="block text-sm font-medium text-gray-700">@lang("Preferred date")</label>
                    <x-text-input type="date" id="preferred_date" name="preferred_date" class="mt-1" value="{{ old('preferred_date', $annonce->preferred_date) }}" required />
                    <x-input-error :messages="$errors->get('preferred_date')" class="mt-1" />
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">@lang("Price") (€)</label>
                    <x-text-input id="price" name="price" type="number" step="0.01" class="mt-1" value="{{ old('price', $annonce->price) }}" />
                    <x-input-error :messages="$errors->get('price')" class="mt-1" />
                </div>

                <div>
                    <label for="constraints" class="block text-sm font-medium text-gray-700">@lang("Constraints")</label>
                    <textarea id="constraints" name="constraints" rows="2" class="mt-1 block w-full border border-gray-300 rounded">{{ old('constraints', $annonce->constraints) }}</textarea>
                    <x-input-error :messages="$errors->get('constraints')" class="mt-1" />
                </div>

                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700">@lang("Photo")</label>
                    @if ($annonce->photo)
                        <img src="{{ asset('storage/' . $annonce->photo) }}" alt="Current photo" class="w-24 h-24 object-cover mb-2 rounded">
                    @endif
                    <input type="file" name="photo" id="photo" class="mt-1 block w-full border border-gray-300 rounded" />
                    <x-input-error :messages="$errors->get('photo')" class="mt-1" />
                </div>

                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    @lang("Save changes")
                </button>
            </form>
        </div>
    </div>
</x-app-layout>