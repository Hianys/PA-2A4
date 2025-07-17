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
        if (!auth()->user()->isDelivery() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        // Affiche uniquement les annonces de type transport non encore entièrement prises en charge
        $annonces = Annonce::where('type', 'transport')
            ->whereDoesntHave('segments', function ($query) {
            })
            ->where('status', '!=', 'archivée')
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
}

