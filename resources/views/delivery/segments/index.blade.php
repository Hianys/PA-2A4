<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Mes livraisons</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 space-y-6">
        @if ($segments->count())
            <div class="space-y-4">
                @foreach ($segments as $segment)
                    <div class="border p-4 rounded shadow bg-white">
                        <div class="flex justify-between">
                            <div>
                                <p class="text-sm text-gray-500">
                                    @lang('Announce') :
                                    <a href="{{ route('delivery.annonces.show', $segment->annonce_id) }}"
                                       class="text-indigo-600 hover:underline">
                                        {{ $segment->annonce->title ?? @lang('deleted announcement') }}
                                    </a>
                                </p>
                                <p class="font-semibold text-lg">
                                    {{ $segment->from_city }} → {{ $segment->to_city }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    @lang('Preferred date') : {{ \Carbon\Carbon::parse($segment->annonce->preferred_date)->format('d/m/Y') }}
                                </p>
                            </div>

                            <div class="self-center space-x-2">
                                @if ($segment->status === 'pending')
                                    <form action="{{ route('segments.updateStatus', $segment) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="in_progress">
                                        <button type="submit" class="text-xs bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">
                                            @lang('Start')
                                        </button>
                                    </form>
                                @elseif ($segment->status === 'in_progress')
                                    <form action="{{ route('segments.updateStatus', $segment) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="delivered">
                                        <button type="submit" class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">
                                            @lang('Mark as delivered')
                                        </button>
                                    </form>
                                @elseif ($segment->status === 'delivered')
                                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">@lang('Delivered')</span>
                                @else
                                    <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">@lang('Unknown')</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500">@lang('No ongoing deliveries.')</p>
        @endif
    </div>
</x-app-layout>
