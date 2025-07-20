<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use App\Services\WalletService;

class AnnonceController extends Controller
{
    protected $walletService;

    public function __construct(\App\Services\WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function index()
{
    $user = auth()->user();

    if (!$user->isDelivery() && !$user->isAdmin()) {
        abort(403);
    }

    // Annonces avec un segment pris en charge par ce livreur
    $annoncesViaSegments = \App\Models\Annonce::whereHas('segments', function ($query) use ($user) {
        $query->where('delivery_id', $user->id);
    })->get();

    // Annonces où il est directement désigné comme livreur
    $annoncesDirectes = \App\Models\Annonce::where('livreur_id', $user->id)->get();

    // Fusion des deux collections
    $annonces = $annoncesViaSegments->merge($annoncesDirectes)->sortByDesc('created_at');

    return view('delivery.annonces.index', compact('annonces'));
}

    public function show(Annonce $annonce)
    {
        if (!auth()->user()->isDelivery() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($annonce->status === 'archivée') {
            abort(404, 'Cette annonce est archivée.');
        }
        $annonce->load('segments.delivery');
        $segments = $annonce->segments;

        return view('delivery.annonces.show', compact('annonce', 'segments'));
    }

    public function confirmDelivery(Request $request, \App\Models\Annonce $delivery)
    {
        $livreur = auth()->user();
        $client = $delivery->user;
        $amount = $delivery->price;

        $this->walletService->unblockTo(
            $client,
            $livreur,
            $amount,
            "Déblocage livraison #{$delivery->id} par le livreur."
        );

        $delivery->update([
            'status' => 'complétée'
        ]);

        return redirect()
            ->route('delivery.annonces.show', $delivery)
            ->with('success', 'Livraison confirmée et paiement débloqué.');
    }

        public function confirmerAnnonce(Request $request, Annonce $annonce)
{
    $prestataire = $annonce->acceptedBy; // même remarque
    $amount = $annonce->price;

    if ($prestataire->wallet->blocked_balance < $amount) {
        return back()->with('error', 'Fonds insuffisants.');
    }

    // Débloquer et transférer
    $prestataire->wallet->blocked_balance -= $amount;
    $prestataire->wallet->balance += $amount;
    $prestataire->wallet->save();

    // Valider l’annonce
    $annonce->is_confirmed = true;
    $annonce->save();

    // Mettre à jour la transaction
    $transaction = $prestataire->wallet->transactions()
        ->where('type', 'service')
        ->where('status', 'pending')
        ->latest()
        ->first();

    if ($transaction) {
        $transaction->status = 'completed';
        $transaction->save();
    }

    return back()->with('success', 'Service confirmé. Paiement libéré.');
}

    public function marquerEnAttenteDePaiement(Annonce $annonce)
{
    if ($annonce->status !== 'prise en charge') {
        return back()->with('error', 'Cette annonce n’est pas encore en prise en charge.');
    }

    // Vérifier que le livreur actuel a bien participé à l’annonce
    if ($annonce->livreur_id !== auth()->id()) {
        return back()->with('error', 'Vous n’êtes pas autorisé à confirmer cette livraison.');
    }

    // On passe au statut "en attente de paiement"
    $annonce->status = 'en attente de paiement';
    $annonce->save();

    return back()->with('success', 'Annonce marquée comme terminée. En attente de paiement.');
}

    public function mesAnnonces()
{
    $user = auth()->user();

    // Annonces où le livreur est principal
    $direct = Annonce::where('livreur_id', $user->id);

    // Annonces où le livreur a participé via un segment
    $segments = Annonce::whereHas('segments', function ($q) use ($user) {
        $q->where('delivery_id', $user->id);
    });

    $annonces = $direct->union($segments)->get();

    return view('delivery.annonces.mes', compact('annonces'));
}
}

