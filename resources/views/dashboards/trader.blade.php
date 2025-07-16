<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
            @lang("Welcome"), {{ Auth::user()->name }} 👋
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Liens de navigation --}}
            <div class="flex flex-col sm:flex-row justify-between gap-4 mb-6">
                <a href="{{ route('commercant.annonces.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 text-center">
                    @lang("Create a new announcement")
                </a>
                <a href="{{ route('commercant.annonces.index') }}" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 text-center">
                    @lang("View my announcements")
                </a>
                <a href="{{ route('commercant.profile.edit') }}" class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600 text-center">
                    @lang("My store profile")
                </a>
            </div>

            {{-- Statistiques des annonces --}}
            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">@lang("Statistics")</h3>
                <div class="flex flex-wrap gap-8">
                    <div>
                        <div class="text-2xl font-bold text-indigo-600">{{ $annonces->where('status', 'publiée')->count() }}</div>
                        <div>@lang("Open")</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-yellow-600">{{ $annonces->where('status', 'taken')->count() }}</div>
                        <div>@lang("Accepted")</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-green-600">{{ $annonces->where('status', 'complétée')->count() }}</div>
                        <div>@lang("Completed")</div>
                    </div>
                </div>
            </div>

            {{-- Récap des annonces --}}
            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">@lang("Your Current Transport Announcements")</h3>
                @if ($annonces->count())
                    <ul class="space-y-3">
                        @foreach ($annonces as $annonce)
                            <li class="border rounded p-4">
                                <div class="flex justify-between">
                                    <div>
                                        <h4 class="font-bold">{{ $annonce->title }}</h4>
                                        <p class="text-sm text-gray-500">{{ $annonce->description }}</p>
                                        <p class="text-sm text-gray-600 mt-1">
                                            <strong>@lang("Status"):</strong> {{ ucfirst($annonce->status) }}
                                            <br>
                                            <strong>@lang("Delivery address"):</strong> {{ $annonce->to_city }}
                                        </p>
                                    </div>
                                    <div class="text-sm text-right text-gray-500">
                                        @lang("Date"): {{ \Carbon\Carbon::parse($annonce->preferred_date)->format('d/m/Y') }}
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-600 dark:text-gray-300">@lang("No current announcements.")</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>