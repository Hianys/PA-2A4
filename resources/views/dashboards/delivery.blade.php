<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
            @lang("welcome"), {{ Auth::user()->name }} 👋
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">@lang("Client Announcements")</h3>
                <a href="{{ route('delivery.annonces.index') }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    @lang("Access Announcements")
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">@lang("My Deliveries")</h3>
                <a href="{{ route('delivery.segments.index') }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    @lang("Access My Deliveries")
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">@lang("Delivery History")</h3>
                <p class="text-gray-600 dark:text-gray-300">@lang("Your delivery history will appear here.")</p>
            </div>

        </div>
    </div>
</x-app-layout>
