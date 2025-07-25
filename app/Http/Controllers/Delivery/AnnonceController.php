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
    $prestataire = $annonce->acceptedBy; 
    $amount = $annonce->price;

    if ($prestataire->wallet->blocked_balance < $amount) {
        return back()->with('error', 'Fonds insuffisants.');
    }

    // Débloque et transférereuh les sous
    $prestataire->wallet->blocked_balance -= $amount;
    $prestataire->wallet->balance += $amount;
    $prestataire->wallet->save();

    $annonce->is_confirmed = true;
    $annonce->save();

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

    // Vérification si il s'agit d'un' livreureuh
    if (!$livreur->isDelivery()) {
        abort(403, 'Vous n’êtes pas livreur.');
    }

    // verif si c'est un des bon livreurs et pas un imposteureuh
    $segments = $annonce->segments()->where('delivery_id', $livreur->id)->get();
    if ($segments->isEmpty()) {
        return back()->with('error', 'Vous ne participez pas à cette livraison.');
    }

    foreach ($segments as $segment) {
        $segment->status = 'en attente de paiement';
        $segment->save();
    }

    if ($annonce->status !== 'en attente de paiement') {
        $annonce->status = 'en attente de paiement';
        $annonce->save();
    }

    return back()->with('success', 'Annonce et segments marqués en attente de paiement.');
}


    public function mesAnnonces()
{
    $user = auth()->user();

    // Annonces où le livreur est seuleuuuh au mondeuh
    $direct = Annonce::where('livreur_id', $user->id);

    
    $segments = Annonce::whereHas('segments', function ($q) use ($user) {
        $q->where('delivery_id', $user->id);
    });

    $annonces = $direct->union($segments)->get();

    return view('delivery.annonces.mes', compact('annonces'));
}
}

