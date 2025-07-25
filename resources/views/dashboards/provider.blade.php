<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
            @lang("Welcome"), {{ Auth::user()->name }} 
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Liens de navigation --}}
            <div class="flex flex-col sm:flex-row justify-between gap-4 mb-6">
                <a href="{{ route('provider.annonces.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 text-center">
                    @lang("Browse Available Missions")
                </a>

                <a href="{{ route('provider.annonces.missions') }}" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 text-center">
                    @lang("View Accepted & Completed Missions")
                </a>
            </div>

            {{-- Prochaines missions --}}
            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">@lang("Upcoming Services")</h3>

                @php
                    $missions_upcoming = $missions->filter(fn($m) => $m->status === 'prise en charge' && $m->preferred_date >= now());
                @endphp

                @if ($missions_upcoming->count())
                    <ul class="space-y-3">
                        @foreach ($missions_upcoming as $mission)
                            <li class="border rounded p-4">
                                <div class="flex justify-between">
                                    <div>
                                        <h4 class="font-bold">{{ $mission->title }}</h4>
                                        <p class="text-sm text-gray-500">{{ $mission->description }}</p>
                                        <p class="text-sm text-gray-600 mt-1">
                                            <strong>@lang("Status"):</strong> {{ ucfirst($mission->status) }}
                                        </p>
                                    </div>
                                    <div class="text-sm text-right text-gray-500">
                                        @lang("Date"): {{ \Carbon\Carbon::parse($mission->preferred_date)->format('d/m/Y') }}
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-600 dark:text-gray-300">@lang("No upcoming missions at this time.")</p>
                @endif
            </div>

            {{-- Missions passées et complétées --}}
            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">@lang("Past & Completed Services")</h3>

                @php
                    $missions_past = $missions->filter(fn($m) => $m->status === 'complétée' || $m->preferred_date < now());
                @endphp

                @if ($missions_past->count())
                    <ul class="space-y-3">
                        @foreach ($missions_past as $mission)
                            <li class="border rounded p-4 bg-gray-100 dark:bg-gray-700">
                                <div class="flex justify-between">
                                    <div>
                                        <h4 class="font-bold">{{ $mission->title }}</h4>
                                        <p class="text-sm text-gray-500">{{ $mission->description }}</p>
                                        <p class="text-sm mt-1">
                                            <strong>@lang("Status"):</strong> {{ ucfirst($mission->status) }}
                                        </p>
                                    </div>
                                    <div class="text-sm text-right text-gray-500">
                                        @lang("Date"): {{ \Carbon\Carbon::parse($mission->preferred_date)->format('d/m/Y') }}
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-600 dark:text-gray-300">@lang("No past or completed missions yet.")</p>
                @endif
            </div>

            {{-- Calendriéeuh (topmoumouteuh) --}}
            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xl font-semibold">@lang("Calendar")</h3>
                    <button id="toggleCalendar" class="text-gray-500 hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>

                <div id="calendar-container" class="mt-4 hidden">
                    <div id="calendar"></div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">@lang("Revenue for this month")</h3>
                <p class="text-2xl font-bold text-green-600">{{ $revenus }} €</p>
            </div>
        </div>
    </div>

    @php
    $events = $missions->map(function ($mission) {
        return [
            'title' => $mission->title,
            'start' => $mission->preferred_date,
            'url' => url('/prestataire/annonces/' . $mission->id),
        ];
    });
    @endphp

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const missions = @json($events);

            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                events: missions,
                eventClick: function (info) {
                    info.jsEvent.preventDefault();
                    if (info.event.url) {
                        window.location.href = info.event.url;
                    }
                }
            });

            calendar.render();

            document.getElementById('toggleCalendar').addEventListener('click', function () {
                document.getElementById('calendar-container').classList.toggle('hidden');
            });
        });
    </script>
    @endpush
</x-app-layout>