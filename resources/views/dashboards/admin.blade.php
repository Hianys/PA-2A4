<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">
            Tableau de bord administrateur
        </h2>
    </x-slot>

    <div class="py-12 px-6">
        <p>Bienvenue, {{ Auth::user()->name }}.</p>
        <p>En tant qu’administrateur, vous pouvez gérer les utilisateurs et consulter les différents espaces.</p>
    </div>
</x-app-layout>
