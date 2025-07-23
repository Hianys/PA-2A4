<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">@lang("My Transport Announcements")</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 space-y-8">

        {{-- Boutons actions --}}
        <div class="flex justify-between mb-2">
            <a href="{{ route('trader.dashboard') }}" class="text-indigo-600 hover:underline text-sm">
                ← @lang("Back to dashboard")
            </a>
            <a href="{{ route('commercant.annonces.create') }}"
                class="bg-indigo-600 text-white px-3 py-2 rounded hover:bg-indigo-700 text-sm">
                @lang("Create a new announcement")
            </a>
        </div>

        {{-- Messages de session --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded">
                {{ session('error') }}
            </div>
        @endif

        @php
            $publiées = $annonces->where('status', 'publiée');
            $prises = $annonces->where('status', 'prise en charge');
            $complétées = $annonces->where('status', 'complétée');
        @endphp

        {{-- Bloc : Annonces publiées --}}
        @if($publiées->count())
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">@lang("Published announcements")</h3>
                <ul class="space-y-4">
                    @foreach ($publiées as $annonce)
                        <li class="border rounded p-4 flex justify-between items-center">
                            <div>
                                <a href="{{ route('commercant.annonces.show', $annonce) }}" class="font-bold hover:underline">
                                    {{ $annonce->title }}
                                </a>
                                <div class="text-sm text-gray-600 mt-1">{{ $annonce->description }}</div>
                                <div class="text-sm text-gray-500 mt-2 space-y-1">
                                    <div><strong>@lang("Departure (your store)"):</strong> {{ $annonce->from_city }}</div>
                                    <div><strong>@lang("Delivery address"):</strong> {{ $annonce->to_city }}</div>
                                    <div><strong>@lang("Price"):</strong> {{ $annonce->price }} €</div>
                                    <div><strong>@lang("Status"):</strong> {{ ucfirst($annonce->status) }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                @if ($annonce->photo)
                                    <img src="{{ asset('storage/' . $annonce->photo) }}" alt="@lang('Photo')" class="w-16 h-16 object-cover rounded">
                                @endif
                                <div class="mt-2">
                                    <a href="{{ route('commercant.annonces.edit', $annonce) }}" class="text-blue-600 hover:underline text-xs">@lang("Edit")</a>
                                </div>
                                <form action="{{ route('commercant.annonces.destroy', $annonce) }}" method="POST" class="mt-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-xs" onclick="return confirm('@lang('Are you sure?')')">
                                        @lang("Delete")
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Bloc : Annonces prises en charge --}}
        @if($prises->count())
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">@lang("Accepted announcements")</h3>
                <ul class="space-y-4">
                    @foreach ($prises as $annonce)
                        <li class="border rounded p-4 flex justify-between items-center">
                            <div>
                                <a href="{{ route('commercant.annonces.show', $annonce) }}" class="font-bold hover:underline">
                                    {{ $annonce->title }}
                                </a>
                                <div class="text-sm text-gray-600 mt-1">{{ $annonce->description }}</div>
                                <div class="text-sm text-gray-500 mt-2 space-y-1">
                                    <div><strong>@lang("Departure (your store)"):</strong> {{ $annonce->from_city }}</div>
                                    <div><strong>@lang("Delivery address"):</strong> {{ $annonce->to_city }}</div>
                                    <div><strong>@lang("Price"):</strong> {{ $annonce->price }} €</div>
                                    <div><strong>@lang("Status"):</strong> {{ ucfirst($annonce->status) }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                @if ($annonce->photo)
                                    <img src="{{ asset('storage/' . $annonce->photo) }}" alt="@lang('Photo')" class="w-16 h-16 object-cover rounded">
                                @endif
                                <div class="mt-2">
                                    <a href="{{ route('commercant.annonces.edit', $annonce) }}" class="text-blue-600 hover:underline text-xs">@lang("Edit")</a>
                                </div>
                                <form action="{{ route('commercant.annonces.destroy', $annonce) }}" method="POST" class="mt-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-xs" onclick="return confirm('@lang('Are you sure?')')">
                                        @lang("Delete")
                                    </button>
                                </form>
                                {{-- Bouton "Marquer comme complétée" --}}
                                <form action="{{ route('commercant.annonces.complete', $annonce) }}" method="POST" class="mt-1">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-green-600 hover:underline text-xs" onclick="return confirm('Confirmer la fin de la livraison ?')">
                                        Marquer comme complétée
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Bloc : Annonces complétées --}}
        @if($complétées->count())
            <div class="bg-gray-50 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">@lang("Completed announcements")</h3>
                <ul class="space-y-4">
                    @foreach ($complétées as $annonce)
                        <li class="border rounded p-4 flex justify-between items-center bg-gray-100">
                            <div>
                                <a href="{{ route('commercant.annonces.show', $annonce) }}" class="font-bold hover:underline">
                                    {{ $annonce->title }}
                                </a>
                                <div class="text-sm text-gray-600 mt-1">{{ $annonce->description }}</div>
                                <div class="text-sm text-gray-500 mt-2 space-y-1">
                                    <div><strong>@lang("Departure (your store)"):</strong> {{ $annonce->from_city }}</div>
                                    <div><strong>@lang("Delivery address"):</strong> {{ $annonce->to_city }}</div>
                                    <div><strong>@lang("Price"):</strong> {{ $annonce->price }} €</div>
                                    <div><strong>@lang("Status"):</strong> {{ ucfirst($annonce->status) }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                @if ($annonce->photo)
                                    <img src="{{ asset('storage/' . $annonce->photo) }}" alt="@lang('Photo')" class="w-16 h-16 object-cover rounded">
                                @endif
                                <div class="mt-2">
                                    <a href="{{ route('commercant.annonces.edit', $annonce) }}" class="text-blue-600 hover:underline text-xs">@lang("Edit")</a>
                                </div>
                                <form action="{{ route('commercant.annonces.destroy', $annonce) }}" method="POST" class="mt-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-xs" onclick="return confirm('@lang('Are you sure?')')">
                                        @lang("Delete")
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Si aucune annonce --}}
        @if($publiées->isEmpty() && $prises->isEmpty() && $complétées->isEmpty())
            <p class="text-center text-gray-500">@lang("No announcements yet.")</p>
        @endif

    </div>
</x-app-layout>