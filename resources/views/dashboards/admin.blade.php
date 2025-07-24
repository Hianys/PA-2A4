<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">@lang("Admin Dashboard")</h2>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <a href="{{ route('admin.users.index') }}" class="bg-white shadow p-6 rounded hover:bg-gray-50">
            <h3 class="text-lg font-semibold mb-2">Utilisateurs</h3>
            <p class="text-gray-600 text-sm">Voir et gérer tous les utilisateurs.</p>
        </a>

        <a href="{{ route('admin.annonces.index') }}" class="bg-white shadow p-6 rounded hover:bg-gray-50">
            <h3 class="text-lg font-semibold mb-2">Annonces</h3>
            <p class="text-gray-600 text-sm">Consulter et modérer les annonces.</p>
        </a>

        <a href="{{ route('admin.documents.index') }}" class="bg-white shadow p-6 rounded hover:bg-gray-50">
            <h3 class="text-lg font-semibold mb-2">Documents</h3>
            <p class="text-gray-600 text-sm">Valider les documents des utilisateurs.</p>
        </a>

        <a href="#" class="bg-white shadow p-6 rounded hover:bg-gray-50">
            <h3 class="text-lg font-semibold mb-2">Paiements</h3>
            <p class="text-gray-600 text-sm">Visualiser les transactions financières.</p>
        </a>
    </div>
</x-app-layout>
