<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">@lang("My Announcements")</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 space-y-8">

        {{-- Retour --}}
        <div>
            <a href="{{ route('client.dashboard') }}" class="text-indigo-600 hover:underline text-sm">
                ← @lang("Back to dashboard")
            </a>
        </div>

        {{-- Formulaire --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">@lang("New Announcement")</h3>

            <form method="POST" action="{{ route('client.annonces.store') }}" class="space-y-4 relative">
                @csrf

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">@lang("Title")</label>
                    <x-text-input id="title" name="title" class="mt-1" required />
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">@lang("Description")</label>
                    <x-textarea id="description" name="description" rows="4" class="mt-1" />
                </div>

                <div>
                    <label for="preferred_date" class="block text-sm font-medium text-gray-700">@lang("Preferred date")</label>
                    <x-text-input type="date" id="preferred_date" name="preferred_date" class="mt-1" />
                    <x-input-error :messages="$errors->get('preferred_date')" class="mt-1" />
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">@lang("Type")</label>
                    <select name="type" id="type" onchange="toggleFields()" class="mt-1 block w-full rounded border-gray-300">
                        <option value="transport">@lang("Transport")</option>
                        <option value="service">@lang("Service")</option>
                    </select>
                </div>

                {{-- Champs spécifiques au transport --}}
                <div id="transport-fields" class="space-y-4">
                    <div class="relative">
                        <label for="from_city" class="block text-sm font-medium text-gray-700">@lang("Departure city")</label>
                        <x-text-input id="from_city" name="from_city" class="mt-1" autocomplete="off" />
                        <x-input-error :messages="$errors->get('from_city')" class="mt-1" />
                        <ul id="from_city_suggestions" class="absolute z-50 w-full bg-white border border-gray-200 rounded shadow hidden"></ul>
                    </div>

                    <div class="relative">
                        <label for="to_city" class="block text-sm font-medium text-gray-700">@lang("Arrival city")</label>
                        <x-text-input id="to_city" name="to_city" class="mt-1" autocomplete="off" />
                        <x-input-error :messages="$errors->get('to_city')" class="mt-1" />

                        <ul id="to_city_suggestions" class="absolute z-50 w-full bg-white border border-gray-200 rounded shadow hidden"></ul>
                    </div>
                </div>

                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    @lang("Create Announcement")
                </button>
            </form>
        </div>

        {{-- Liste des annonces --}}
        @if ($annonces->count())
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">@lang("Your Announcements")</h3>
                <ul class="space-y-4">
                    @foreach ($annonces as $annonce)
                        <li class="border rounded p-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <a href="{{ route('client.annonces.show', $annonce) }}">
                                        <h4 class="font-bold">{{ $annonce->title }}
                                    </a>
                                        <span class="ml-2 text-xs px-2 py-1 rounded bg-gray-200 text-gray-800">
                                            {{ ucfirst($annonce->type) }}
                                        </span>
                                    </h4>
                                    <p class="text-sm text-gray-600 mt-1">{{ $annonce->description }}</p>
                                </div>
                                @if ($annonce->type === 'transport')
                                    <div class="text-sm text-right text-gray-500">
                                        {{ $annonce->from_city }} → {{ $annonce->to_city }}<br>
                                        {{ \Carbon\Carbon::parse($annonce->preferred_date)->format('d/m/Y') }}
                                    </div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <p class="text-center text-gray-500">@lang("No announcements available at the moment.")</p>
        @endif
    </div>

    {{-- Scripts --}}
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
