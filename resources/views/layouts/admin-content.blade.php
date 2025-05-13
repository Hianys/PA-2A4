<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r p-4">
        <h1 class="text-lg font-bold mb-4">Admin EcoDeli</h1>
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="block py-2">Utilisateurs</a>
            <a href="#" class="block py-2">Annonces</a>
            <a href="#" class="block py-2">Statistiques</a>
        </nav>
    </aside>

    <!-- Main content -->
    <div class="flex-1 p-6">
        {{ $slot }}
    </div>
</div>
