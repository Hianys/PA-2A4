<?php

namespace App\Http\Controllers\Trader;

use App\Services\PaymentService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Annonce;
use App\Http\Requests\TraderProfileUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;


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

    if ($user->isTrader() && !$user->kbis_valide) {
        return redirect()->route('trader.dashboard')
            ->with('error', 'Vous ne pouvez pas publier d\'annonce tant que votre KBIS n\'est pas validé.');
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
    'preferred_date' => ['required', 'date', 'after_or_equal:today'],
    'price' => 'nullable|numeric',
    'constraints' => 'nullable|string',
    'kbis' => 'nullable|image',
    'from_city' => 'required|string|max:255',
    'to_city' => 'required|string|max:255',
]);

$annonce = new Annonce($validated);

// Coordonnées GPS (si valides)
$annonce->from_lat = is_numeric($request->from_lat) ? $request->from_lat : null;
$annonce->from_lng = is_numeric($request->from_lng) ? $request->from_lng : null;
$annonce->to_lat   = is_numeric($request->to_lat)   ? $request->to_lat   : null;
$annonce->to_lng   = is_numeric($request->to_lng)   ? $request->to_lng   : null;

// Infos auto
$annonce->user_id = $user->id;
$annonce->type = 'transport';
$annonce->status = 'publiée';

// Fichier kbis si envoyé
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

public function markCompleted(Annonce $annonce, PaymentService $paymentService)
{
    $user = Auth::user();

    if ((!$user->isTrader() && !$user->isAdmin()) || $annonce->user_id !== $user->id) {
        abort(403);
    }

    if ($annonce->status !== 'prise en charge') {
        return redirect()->back()->with('error', 'La mission n\'est pas en cours.');
    }

    try {
        // Étape 1 : Paiement et blocage
        $paymentService->processSegmentedPayment($annonce);

        // Étape 2 : Libération immédiate des fonds
        $segments = $annonce->segments()->where('status', 'accepté')->get();
        $grouped = $segments->groupBy('delivery_id');

        foreach ($grouped as $livreurId => $livreurSegments) {
            $livreur = $livreurSegments->first()->delivery;

            // Transactions en attente
            $transactions = $livreur->wallet->transactions()
                ->where('status', 'pending')
                ->where('type', 'delivery')
                ->latest()
                ->get();

            foreach ($transactions as $transaction) {
                $amount = $transaction->amount;

                if ($livreur->wallet->blocked_balance >= $amount) {
                    $livreur->wallet->blocked_balance -= $amount;
                    $livreur->wallet->balance += $amount;

                    $transaction->status = 'completed';
                    $transaction->save();
                }
            }

            $livreur->wallet->save();
        }

        // Mise à jour de l’annonce
        $annonce->status = 'complétée';
        $annonce->is_paid = true;
        $annonce->is_confirmed = true;
        $annonce->save();

        return redirect()->route('commercant.annonces.index')->with('success', 'Mission complétée et fonds versés aux livreurs.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Erreur lors du paiement : ' . $e->getMessage());
    }
}

   public function updateProfile(\App\Http\Requests\TraderProfileUpdateRequest $request)
{
    $user = Auth::user();

    if (!$user->isTrader() && !$user->isAdmin()) {
        abort(403);
    }

    $validated = $request->validated();

    \Log::info('Kbis reçu', ['kbis' => $request->file('kbis')]);

    if ($request->hasFile('kbis')) {
        $kbisFile = $request->file('kbis');

        if (!$kbisFile->isValid()) {
            \Log::error(' Fichier Kbis invalide', ['error' => $kbisFile->getErrorMessage()]);
            return back()->withErrors(['kbis' => 'Erreur lors de l\'upload du fichier.']);
        }

        // Supprimer l'ancien fichier s'il existe
        if ($user->kbis && \Storage::disk('public')->exists($user->kbis)) {
            \Storage::disk('public')->delete($user->kbis);
        }

        // Générer un nom de fichier propre
        $extension = $kbisFile->getClientOriginalExtension();
        $filename = uniqid('kbis_') . ($extension ? '.' . $extension : '');

        // Stocker dans le disque "public" (storage/app/public/commercant_docs)
        $kbisFile->storeAs('commercant_docs', $filename, ['disk' => 'public']);
        $filePath = 'commercant_docs/' . $filename;

        \Log::info('✅ Kbis stocké à', ['path' => $filePath]);

        // Enregistrer le chemin relatif dans la base
        $user->kbis = $filePath;
    }

    // Mettre à jour les champs texte
    if (isset($validated['enseigne'])) {
        $user->enseigne = $validated['enseigne'];
    }

    if (isset($validated['adresse'])) {
        $user->adresse = $validated['adresse'];
    }

    \Log::info('Avant save', ['kbis' => $user->kbis, 'id' => $user->id]);
    $user->save();
    \Log::info('Après save', ['kbis' => $user->fresh()->kbis, 'id' => $user->id]);

    return redirect()->route('commercant.profile.edit')->with('success', 'Profil commerçant mis à jour !');
}

    public function showConsentForm()
{
    $user = Auth::user();
    return view('trader.consentement', ['enseigne' => $user->enseigne]);
}

   public function generateConsentPdf(Request $request)
{
    $request->validate([
        'accept_terms' => 'nullable',
        'enseigne' => 'required|string|max:255',
    ]);

    $user = Auth::user();

    $pdf = Pdf::loadView('pdfs.consentement', [
        'enseigne' => $request->enseigne,
        'date' => now()->format('d/m/Y'),
        'user' => $user,
    ]);

    return $pdf->download('consentement_' . $user->id . '.pdf');
}   

public function validerConsentement(Request $request)
{
    $request->validate([
        'accept_terms' => 'nullable', // facultatif si décoché
        'enseigne' => 'required|string|max:255',
    ]);

    $user = Auth::user();
    $user->enseigne = $request->enseigne;
    $user->consentement_valide = $request->has('accept_terms');
    $user->save();

    return redirect()->route('commercant.consentement.form')->with('success', 'Consentement mis à jour.');
}

public function telechargerPdf()
{
    $user = Auth::user();

    $pdf = Pdf::loadView('pdfs.consentement', [
        'enseigne' => $user->enseigne,
        'date' => now()->format('d/m/Y'),
        'user' => $user,
    ]);

    return $pdf->download('consentement_' . $user->id . '.pdf');
}

    public function payAnnonce(Request $request, \App\Models\Annonce $annonce)
{
    $commercant = auth()->user();

    // Vérifie que le commerçant est bien propriétaire de l'annonce
    if ($annonce->user_id !== $commercant->id) {
        abort(403);
    }

    // Vérifie qu’un livreur a bien été assigné
    $livreur = $annonce->livreur;
    if (!$livreur) {
        return back()->with('error', 'Aucun livreur assigné à cette annonce.');
    }

    $amount = $annonce->price;

    // Vérifie solde
    if ($commercant->wallet->balance < $amount) {
        return back()->with('error', 'Solde insuffisant.');
    }

    // Débit du commerçant
    $commercant->wallet->balance -= $amount;
    $commercant->wallet->save();

    $commercant->wallet->transactions()->create([
        'type' => 'delivery',
        'amount' => $amount,
        'status' => 'completed', // car l’argent a été débité immédiatement
    ]);

    // Blocage sur le compte du livreur
    $livreur->wallet->blocked_balance += $amount;
    $livreur->wallet->save();

    // Créer transaction bloquée pour le livreur
    $livreur->wallet->transactions()->create([
        'type' => 'delivery',
        'amount' => $amount,
        'status' => 'pending', // elle sera confirmée plus tard
    ]);

    // Marquer annonce comme payée
    $annonce->is_paid = true;
    $annonce->save();

    return back()->with('success', 'Paiement effectué et fonds bloqués pour le livreur.');
}

public function confirmer(Request $request, \App\Models\Annonce $annonce)
{
    $commercant = auth()->user();

    if ($annonce->user_id !== $commercant->id) {
        abort(403);
    }

    $livreur = $annonce->livreur;
    $amount = $annonce->price;

    if (!$livreur) {
        return back()->with('error', 'Aucun livreur assigné.');
    }

    if ($livreur->wallet->blocked_balance < $amount) {
        return back()->with('error', 'Montant bloqué insuffisant.');
    }

    // Déblocage des fonds
    $livreur->wallet->blocked_balance -= $amount;
    $livreur->wallet->balance += $amount;
    $livreur->wallet->save();

    // Marque transaction comme complétée
    $transaction = $livreur->wallet->transactions()
        ->where('type', 'delivery')
        ->where('status', 'pending')
        ->latest()
        ->first();

    if ($transaction) {
        $transaction->status = 'completed';
        $transaction->save();
    }

    // Mettre à jour l’annonce
    $annonce->is_confirmed = true;
    $annonce->status = 'complétée';
    $annonce->save();

    return back()->with('success', 'Livraison confirmée, paiement libéré au livreur.');
}


    public function confirmPayments(Annonce $annonce)
{
    $user = Auth::user();

    if ((!$user->isTrader() && !$user->isAdmin()) || $annonce->user_id !== $user->id) {
        abort(403);
    }

    if ($annonce->status !== 'complétée') {
        return back()->with('error', 'L\'annonce n\'est pas encore marquée comme complétée.');
    }

    $segments = $annonce->segments()->where('status', 'accepté')->get();
    $grouped = $segments->groupBy('delivery_id');

    foreach ($grouped as $livreurId => $livreurSegments) {
        $livreur = $livreurSegments->first()->delivery;
        $totalAmount = 0;

        // Trouver les transactions en attente
        $transactions = $livreur->wallet->transactions()
            ->where('status', 'pending')
            ->where('type', 'delivery')
            ->latest()
            ->get();

        foreach ($transactions as $transaction) {
            $amount = $transaction->amount;

            if ($livreur->wallet->blocked_balance >= $amount) {
                $livreur->wallet->blocked_balance -= $amount;
                $livreur->wallet->balance += $amount;
                $totalAmount += $amount;

                $transaction->status = 'completed';
                $transaction->save();
            }
        }

        $livreur->wallet->save();
    }

    return back()->with('success', 'Fonds débloqués et transférés aux livreurs.');
}

    public function mesAnnonces()
{
    $user = auth()->user();

    // 1. Annonces où il est le livreur principal
    $annoncesPrincipales = \App\Models\Annonce::where('livreur_id', $user->id)->get();

    // 2. Annonces où il a au moins un segment
    $annoncesSegments = \App\Models\Annonce::whereHas('segments', function ($query) use ($user) {
        $query->where('delivery_id', $user->id);
    })->get();

    // Fusionner et enlever les doublons
    $annonces = $annoncesPrincipales->merge($annoncesSegments)->unique('id');

    return view('delivery.annonces.mes', compact('annonces'));
}

public function facturePdf(Annonce $annonce)
{
    $user = Auth::user();

    $isOwner = $annonce->user_id === $user->id;
    $isLivreur = $annonce->segments()
        ->where('delivery_id', $user->id)
        ->exists();

    if (!$isOwner && !$isLivreur) {
        abort(403, 'Accès non autorisé à cette facture.');
    }

    $segments = $annonce->segments()->where('status', 'accepté')->get();

    $totalDistance = $segments->sum(function ($s) {
        return $this->haversine($s->from_lat, $s->from_lng, $s->to_lat, $s->to_lng);
    });

    $grouped = $segments->groupBy('delivery_id');

    $parts = [];

    foreach ($grouped as $livreurId => $livreurSegments) {
        $livreur = $livreurSegments->first()->delivery;
        $livreurDistance = $livreurSegments->sum(function ($s) {
            return $this->haversine($s->from_lat, $s->from_lng, $s->to_lat, $s->to_lng);
        });
        $part = $totalDistance > 0 ? $livreurDistance / $totalDistance : 0;
        $montant = round($annonce->price * $part, 2);

        $parts[] = [
            'livreur' => $livreur->name,
            'distance' => round($livreurDistance, 2),
            'part' => round($part * 100, 1),
            'montant' => $montant,
        ];
    }

    $pdf = Pdf::loadView('pdfs.facture', [
        'annonce' => $annonce,
        'parts' => $parts,
        'total' => $annonce->price,
    ]);

    return $pdf->download('facture-annonce-' . $annonce->id . '.pdf');
}

private function haversine($lat1, $lon1, $lat2, $lon2)
{
    if (!$lat1 || !$lon1 || !$lat2 || !$lon2) return 0;

    $earthRadius = 6371;
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);
    $deltaLat = $lat2 - $lat1;
    $deltaLon = $lon2 - $lon1;
    $a = sin($deltaLat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($deltaLon / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earthRadius * $c;
}
}