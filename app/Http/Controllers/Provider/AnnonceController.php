<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnonceController extends Controller
{
    // Dashboard commerçant
    public function dashboard()
    {
        $user = Auth::user();
        if (!$user->isTrader() && !$user->isAdmin()) {
            abort(403);
        }

        // Toutes les annonces de transport postées par CE commerçant
        $annonces = Annonce::where('user_id', $user->id)
            ->where('type', 'transport')
            ->latest()
            ->get();

        return view('dashboards.trader', compact('annonces'));
    }

    // Liste des annonces du commerçant
    public function index()
    {
        $user = Auth::user();
        if (!$user->isTrader() && !$user->isAdmin()) {
            abort(403);
        }

        $annonces = Annonce::where('user_id', $user->id)
            ->where('type', 'transport')
            ->latest()
            ->get();

        return view('trader.annonces.index', compact('annonces'));
    }

    // Formulaire de création
    public function create()
    {
        $user = Auth::user();
        if (!$user->isTrader() && !$user->isAdmin()) {
            abort(403);
        }

        // On passe l'adresse préremplie (via la fiche commerçant)
        $adresse = $user->commercantInfo ? $user->commercantInfo->adresse : '';

        return view('trader.annonces.create', compact('adresse'));
    }

    // Sauvegarde annonce
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isTrader() && !$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'preferred_date' => 'required|date',
            'price' => 'nullable|numeric',
            'constraints' => 'nullable|string',
            'photo' => 'nullable|image',
            'to_city' => 'required|string|max:255', // Adresse de livraison
        ]);

        $annonce = new Annonce($validated);
        $annonce->user_id = $user->id;
        $annonce->type = 'transport';
        $annonce->status = 'published';

        // Ajoute l’adresse du commerçant s’il a une fiche
        if ($user->commercantInfo) {
            $annonce->from_city = $user->commercantInfo->adresse;
        }

        if ($request->hasFile('photo')) {
            $annonce->photo = $request->file('photo')->store('annonces', 'public');
        }

        $annonce->save();

        return redirect()->route('commercant.annonces.index')->with('success', 'Annonce créée avec succès.');
    }

    public function show(Annonce $annonce)
    {
        $user = Auth::user();
        if ((!$user->isTrader() && !$user->isAdmin()) || $annonce->user_id !== $user->id) {
            abort(403);
        }

        return view('trader.annonces.show', compact('annonce'));
    }

    public function edit(Annonce $annonce)
    {
        $user = Auth::user();
        if ((!$user->isTrader() && !$user->isAdmin()) || $annonce->user_id !== $user->id) {
            abort(403);
        }

        return view('trader.annonces.edit', compact('annonce'));
    }

    public function update(Request $request, Annonce $annonce)
    {
        $user = Auth::user();
        if ((!$user->isTrader() && !$user->isAdmin()) || $annonce->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'preferred_date' => 'required|date',
            'price' => 'nullable|numeric',
            'constraints' => 'nullable|string',
            'to_city' => 'required|string|max:255',
        ]);

        $annonce->update($validated);

        return redirect()->route('commercant.annonces.index')->with('success', 'Annonce mise à jour avec succès.');
    }

    public function destroy(Annonce $annonce)
    {
        $user = Auth::user();
        if ((!$user->isTrader() && !$user->isAdmin()) || $annonce->user_id !== $user->id) {
            abort(403);
        }

        $annonce->delete();

        return redirect()->route('commercant.annonces.index')->with('success', 'Annonce supprimée.');
    }

    public function markCompleted(Annonce $annonce)
    {
        $user = Auth::user();
        if ((!$user->isTrader() && !$user->isAdmin()) || $annonce->user_id !== $user->id) {
            abort(403);
        }

        if ($annonce->status !== 'taken') {
            return redirect()->back()->with('error', 'La mission n\'est pas en cours.');
        }

        $annonce->status = 'completed';
        $annonce->save();

        return redirect()->route('commercant.annonces.index')->with('success', 'Mission marquée comme complétée.');
    }
};