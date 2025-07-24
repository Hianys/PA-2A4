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

    $annonces = \App\Models\Annonce::where('type', 'transport')
        ->where('status', 'publiée')
        ->latest()
        ->get();

    /*
    //Annonces avec un segment pris en charge par ce livreur
    $annoncesViaSegments = \App\Models\Annonce::whereHas('segments', function ($query) use ($user) {
        $query->where('delivery_id', $user->id);
    })->get();

    // Annonces où il est directement désigné comme livreur
    $annoncesDirectes = \App\Models\Annonce::where('livreur_id', $user->id)->get();

    // Fusion des deux collections
    $annonces = $annoncesViaSegments->merge($annoncesDirectes)->sortByDesc('created_at');*/

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

    // recharge les relations et l'objet
    $annonce->refresh();
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
    $livreur = auth()->user();

    // Vérification
    if (!$livreur->isDelivery()) {
        abort(403, 'Vous n’êtes pas livreur.');
    }

    // Le livreur doit avoir participé à l'annonce
    $segments = $annonce->segments()->where('delivery_id', $livreur->id)->get();
    if ($segments->isEmpty()) {
        return back()->with('error', 'Vous ne participez pas à cette livraison.');
    }

    // Mettre à jour les segments du livreur en "en attente de paiement"
    foreach ($segments as $segment) {
        $segment->status = 'en attente de paiement';
        $segment->save();
    }

    // Mettre l'annonce en "en attente de paiement" (si pas déjà)
    if ($annonce->status !== 'en attente de paiement') {
        $annonce->status = 'en attente de paiement';
        $annonce->save();
    }

    return back()->with('success', 'Annonce et segments marqués en attente de paiement.');
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

