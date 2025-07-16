<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnonceController extends Controller
{
    public function dashboard()
{
    $user = Auth::user();

    if (!$user->isProvider() && !$user->isAdmin()) {
        abort(403);
    }

    // Les missions acceptées ou complétées par le prestataire
    $missions = Annonce::where('provider_id', $user->id)
        ->whereIn('status', ['prise en charge', 'complétée'])
        ->latest()
        ->get();

    // Calcul des revenus du mois
    $revenus = $missions
        ->where('status', 'complétée')
        ->whereBetween('preferred_date', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])
        ->sum('price');

    return view('dashboards.provider', compact('missions', 'revenus'));
}

    public function index()
{
    $user = Auth::user();

    if (!$user->isProvider() && !$user->isAdmin()) {
        abort(403);
    }

    // Affiche uniquement les annonces de type "service" qui n'ont pas encore été acceptées
    $annonces = Annonce::where('type', 'service')
        ->whereNull('provider_id')
        ->where('status', 'publiée')
        ->latest()
        ->get();

    return view('provider.annonces.index', compact('annonces'));
}

    public function create()
    {
        $user = Auth::user();
        if (!$user->isProvider() && !$user->isAdmin()) {
            abort(403);
        }

        return view('provider.annonces.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isProvider() && !$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'preferred_date' => 'required|date',
            'price' => 'nullable|numeric',
            'constraints' => 'nullable|string',
            'photo' => 'nullable|image',
        ]);

        $annonce = new Annonce($validated);
        $annonce->user_id = $user->id;
        $annonce->type = 'service';
        $annonce->status = 'publiée';

        if ($request->hasFile('photo')) {
            $annonce->photo = $request->file('photo')->store('annonces', 'public');
        }

        $annonce->save();

        return redirect()->route('provider.annonces.index')->with('success', 'Annonce créée avec succès.');
    }

    public function show(Annonce $annonce)
{
    $user = Auth::user();

    // Ne montrer que les annonces de type "service"
    if ($annonce->type !== 'service') {
        abort(403);
    }

    // Si la mission est déjà acceptée par un autre prestataire, bloquer l'accès
    if ($annonce->status === 'prise en charge' && $annonce->provider_id !== $user->id) {
        abort(403);
    }

    return view('provider.annonces.show', compact('annonce'));
}

    public function edit(Annonce $annonce)
    {
        $user = Auth::user();
        if ((!$user->isProvider() && !$user->isAdmin()) || $annonce->user_id !== $user->id) {
            abort(403);
        }

        return view('provider.annonces.edit', compact('annonce'));
    }

    public function update(Request $request, Annonce $annonce)
    {
        $user = Auth::user();
        if ((!$user->isProvider() && !$user->isAdmin()) || $annonce->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'preferred_date' => 'required|date',
            'price' => 'nullable|numeric',
            'constraints' => 'nullable|string',
        ]);

        $annonce->update($validated);

        return redirect()->route('provider.annonces.index')->with('success', 'Annonce mise à jour avec succès.');
    }

    public function destroy(Annonce $annonce)
    {
        $user = Auth::user();
        if ((!$user->isProvider() && !$user->isAdmin()) || $annonce->user_id !== $user->id) {
            abort(403);
        }

        $annonce->delete();

        return redirect()->route('provider.annonces.index')->with('success', 'Annonce supprimée.');
    }

    public function markCompleted(Annonce $annonce)
    {
        $user = Auth::user();
        if ((!$user->isProvider() && !$user->isAdmin()) || $annonce->user_id !== $user->id) {
            abort(403);
        }

        if ($annonce->status !== 'prise en charge') {
            return redirect()->back()->with('error', 'La mission n\'est pas en cours.');
        }

        $annonce->status = 'complétée';
        $annonce->save();

        return redirect()->route('provider.annonces.index')->with('success', 'Mission marquée comme complétée.');
    }

    public function accept(Annonce $annonce)
{
    $user = Auth::user();

    // Vérifie que l'annonce est de type "service" et encore disponible
    if ($annonce->type !== 'service' || $annonce->status !== 'publiée') {
        return redirect()->back()->with('error', 'Cette annonce ne peut pas être acceptée.');
    }

    // Vérifie que l'annonce n'est pas déjà prise
    if ($annonce->provider_id !== null) {
        return redirect()->back()->with('error', 'Cette annonce a déjà été acceptée par un autre prestataire.');
    }

    // Associe la mission au prestataire connecté
    $annonce->provider_id = $user->id;
    $annonce->status = 'prise en charge';
    $annonce->save();

    return redirect()->route('provider.dashboard')->with('success', 'Annonce acceptée avec succès.');
}

    public function missions()
{
    $user = Auth::user();

    if (!$user->isProvider() && !$user->isAdmin()) {
        abort(403);
    }

    // Toutes les missions acceptées ou complétées
    $annonces = Annonce::where('provider_id', $user->id)
        ->whereIn('status', ['prise en charge', 'complétée'])
        ->latest()
        ->get();

    return view('provider.annonces.missions', compact('annonces'));
}

}
