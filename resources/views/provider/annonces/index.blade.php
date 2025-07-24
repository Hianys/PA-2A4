<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">@lang("My Service Offers")</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 space-y-8">

        {{-- Back --}}
        <div class="flex justify-between">
            <a href="{{ route('provider.dashboard') }}" class="text-indigo-600 hover:underline text-sm">
                ← @lang("Back to dashboard")
            </a>
            <a href="{{ route('provider.annonces.missions') }}" class="text-indigo-600 hover:underline text-sm">
                @lang("My accepted missions") →
            </a>
        </div>


        {{-- List of announcements --}}
        @if ($annonces->count())
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">@lang("Your Service Offers")</h3>
                <ul class="space-y-4">
                    @foreach ($annonces as $annonce)
                        <li class="border rounded p-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <a href="{{ route('provider.annonces.show', $annonce) }}">
                                        <h4 class="font-bold">{{ $annonce->title }}</h4>
                                    </a>
                                    <p class="text-sm text-gray-600 mt-1">{{ $annonce->description }}</p>

                                    <div class="text-sm text-gray-500 mt-2 space-y-1">
                                        <div><strong>@lang("Price"):</strong> {{ $annonce->price }} €</div>
                                        <div><strong>@lang("Constraints"):</strong> {{ $annonce->constraints }}</div>
                                        <div><strong>@lang("Status"):</strong> {{ $annonce->status }}</div>
                                    </div>
                                </div>
                                <div class="text-sm text-right text-gray-500">
                                    {{ $annonce->preferred_date ? \Carbon\Carbon::parse($annonce->preferred_date)->format('d/m/Y') : '' }}

                                    @if ($annonce->photo)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $annonce->photo) }}" alt="Photo" class="w-16 h-16 object-cover rounded">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <p class="text-center text-gray-500">@lang("No service offers at the moment.")</p>
        @endif
    </div>
</x-app-layout>