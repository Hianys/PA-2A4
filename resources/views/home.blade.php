<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil | Ecodéli</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-800">

<!-- Header -->
<header class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 py-6 flex flex-col md:flex-row items-center justify-between">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12">
            <h1 class="text-2xl font-bold text-indigo-600">Ecodeli</h1>
        </div>
        <div class="flex gap-4 items-center">
            @auth
                <!-- Si l'utilisateur est connecté -->
                <a href="{{ dashboard_route_for(Auth::user()->role) }}"
                   class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow">
                    Accéder à mon espace
                </a>
            @else
                <!-- Sinon, boutons login / register -->
                <a href="{{ route('login') }}" class="px-6 py-3 bg-white text-indigo-600 hover:bg-indigo-100 rounded-lg border border-indigo-600 shadow">
                    Se connecter
                </a>
                <a href="{{ route('register') }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow">
                    S’inscrire
                </a>
            @endauth
        </div>
    </div>
</header>


<section class="py-24 px-6 text-center">
    <h2 class="text-4xl md:text-5xl font-bold mb-4">Réinventons la livraison locale 🌍</h2>
    <p class="text-lg md:text-xl max-w-2xl mx-auto text-gray-600 mb-8">
        Ecodéli connecte commerçants, livreurs, prestataires et clients dans une démarche écoresponsable et locale.
    </p>
    @auth

    @else
        <a href="{{ route('register') }}" class="inline-block px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-lg font-medium shadow">
            Créer un compte
        </a>
    @endauth
</section>

<!-- Footer -->
<footer class="py-6 text-center text-sm text-gray-400>
    Projet annuel — IUT &copy; {{ date('Y') }}
</footer>

</body>
</html>
