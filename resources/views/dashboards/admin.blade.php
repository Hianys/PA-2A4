<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Tableau de bord administrateur</h2>
    </x-slot>

    <x-admin-content>
        <h2 class="text-2xl font-semibold mb-4">Gestion des utilisateurs</h2>
        <p>Bienvenue, {{ Auth::user()->name }}.</p>
    </x-admin-content>
</x-app-layout>
