<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">
            @lang("Announcement details")
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6">
        <div class="bg-white rounded shadow p-6 space-y-4">
            <h3 class="text-2xl font-bold">{{ $annonce->title }}</h3>
            <div class="text-gray-700 mb-2">{{ $annonce->description }}</div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <strong>@lang("Shipping from (your store)"):</strong>
                    <span>{{ $annonce->from_city }}</span>
                </div>
                <div>
                    <strong>@lang("Delivery address"):</strong>
                    <span>{{ $annonce->to_city }}</span>
                </div>
                <div>
                    <strong>@lang("Preferred date"):</strong>
                    <span>{{ \Carbon\Carbon::parse($annonce->preferred_date)->format('d/m/Y') }}</span>
                </div>
                <div>
                    <strong>@lang("Price"):</strong>
                    <span>{{ $annonce->price }} €</span>
                </div>
                <div>
                    <strong>@lang("Constraints"):</strong>
                    <span>{{ $annonce->constraints ?? '-' }}</span>
                </div>
                <div>
                    <strong>@lang("Status"):</strong>
                    <span>{{ ucfirst($annonce->status) }}</span>
                </div>
            </div>
            
            @if($annonce->photo)
                <div class="mt-4">
                    <img src="{{ asset('storage/' . $annonce->photo) }}" alt="Photo" class="w-48 h-48 object-cover rounded">
                </div>
            @endif

            <div class="mt-4 flex gap-4">
                <a href="{{ route('commercant.annonces.edit', $annonce) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">@lang("Edit")</a>
                <form action="{{ route('commercant.annonces.destroy', $annonce) }}" method="POST" onsubmit="return confirm('@lang('Are you sure?')');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        @lang("Delete")
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>