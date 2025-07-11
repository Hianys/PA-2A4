<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Annonce;

class AnnonceController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isDelivery() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        // Affiche uniquement les annonces de type transport non encore entièrement prises en charge
        $annonces = Annonce::where('type', 'transport')
            ->whereDoesntHave('segments', function ($query) {
                // Tu peux adapter cette logique selon la façon dont tu gères la prise en charge
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

}

