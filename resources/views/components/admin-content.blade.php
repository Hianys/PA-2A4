<div>
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r p-4">
            <h1 class="text-lg font-bold mb-4">Admin EcoDeli</h1>
            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block hover:underline">Utilisateurs</a>
                <a href="#" class="block hover:underline">Annonces</a>
                <a href="#" class="block hover:underline">Statistiques</a>
                <a href="#" class="block hover:underline">Paramètres</a>
            </nav>
        </aside>

        <!-- Contenu principal -->
        <div class="flex-1 p-6">
            {{ $slot }}
        </div>
    </div>

</div>
