<?php

namespace App\Http\Controllers\Trader;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Annonce;
use App\Http\Requests\TraderProfileUpdateRequest;
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

        // On passe l'adresse préremplie
        $adresse = $user->adresse;

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
            'kbis' => 'nullable|image',
            'to_city' => 'required|string|max:255', // Adresse de livraison
        ]);

        $annonce = new Annonce($validated);
        $annonce->user_id = $user->id;
        $annonce->type = 'transport';
        $annonce->status = 'published';
        $annonce->from_city = $user->adresse; // adresse du commerçant

        if ($request->hasFile('kbis')) {
            $annonce->kbis = $request->file('kbis')->store('annonces', 'public');
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

    // Gestion du profil commerçant (enseigne, adresse, document)
    public function editProfile()
    {
        $user = Auth::user();
        if (!$user->isTrader() && !$user->isAdmin()) {
            abort(403);
        }
        return view('trader.profile', compact('user'));
    }

   public function updateProfile(\App\Http\Requests\TraderProfileUpdateRequest $request)
{
    $user = Auth::user();
    if (!$user->isTrader() && !$user->isAdmin()) {
        abort(403);
    }

    $validated = $request->validated();

    if ($request->hasFile('kbis')) {
    // Supprimer l'ancien fichier s'il existe
    if ($user->kbis_path && \Storage::disk('public')->exists($user->kbis_path)) {
        \Storage::disk('public')->delete($user->kbis_path);
    }

    // Stocker le nouveau fichier dans storage/app/public/commercant_docs
    $filePath = $request->file('kbis')->store('commercant_docs', 'public');

    // Enregistrer le chemin relatif dans la base de données
    $user->kbis_path = $filePath;
}

    if (isset($validated['enseigne'])) {
        $user->enseigne = $validated['enseigne'];
    }
    if (isset($validated['adresse'])) {
        $user->adresse = $validated['adresse'];
    }

    \Log::info('Avant save', ['kbis_path' => $user->kbis_path, 'id' => $user->id]);
    $user->save();
    \Log::info('Après save', ['kbis_path' => $user->fresh()->kbis_path, 'id' => $user->id]);

    return redirect()->route('commercant.profile.edit')->with('success', 'Profil commerçant mis à jour !');
}
}