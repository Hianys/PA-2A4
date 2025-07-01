<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
            @lang("welcome"), {{ Auth::user()->name }} 👋
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-4 rounded shadow">
                <h3 class="text-lg font-semibold mb-2">@lang("Client Announcements")</h3>
                <p class="text-sm text-gray-600 mb-4">@lang("Manage your offers and requests here.")</p>
                <a href="{{ route('client.annonces.index') }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    @lang("Access My Announcements")
                </a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">@lang("My Deliveries")</h3>
                <p class="text-gray-600">@lang("No scheduled services at this time.")</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">@lang("Delivery History")</h3>
                <p class="text-gray-600">@lang("Your delivery history will appear here.")</p>
            </div>

        </div>
    </div>
</x-app-layout>
