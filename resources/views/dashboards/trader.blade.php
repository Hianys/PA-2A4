<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
            @lang("welcome"), {{ Auth::user()->name }} 👋
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">@lang("Ongoing orders")</h3>
                <p class="text-gray-600 dark:text-gray-300">@lang("No ongoing orders at the moment.")</p>
            </div>

            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">@lang("Active announcements")</h3>
                <p class="text-gray-600 dark:text-gray-300">@lang("Manage your offers and requests here.")</p>
            </div>

            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">@lang("Billing")</h3>
                <p class="text-gray-600 dark:text-gray-300">@lang("Your invoices will be available in this space.")</p>
            </div>

        </div>
    </div>
</x-app-layout>
