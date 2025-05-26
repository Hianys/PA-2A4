<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Annonce;
use App\Models\TransportSegment;

class TransportSegmentController extends Controller
{
    public function index()
    {
        // On récupère uniquement les annonces de type "transport"
        // qui n’ont pas encore été complètement prises en charge
        $annonces = Annonce::where('type', 'transport')->latest()->get();

        return view('delivery.annonces.index', compact('annonces'));
    }

    public function store(Request $request, Annonce $annonce)
    {
        $request->validate([
            'from_city' => 'required|string|max:255',
            'to_city' => 'required|string|max:255',
        ]);

        // Vérification logique optionnelle : validité du segment
        if ($request->from_city === $request->to_city) {
            return back()->with('error', 'La ville de départ et d’arrivée doivent être différentes.');
        }

        // Créer le segment
        TransportSegment::create([
            'annonce_id' => $annonce->id,
            'livreur_id' => auth()->id(),
            'from_city' => $request->from_city,
            'to_city' => $request->to_city,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Segment proposé avec succès.');
    }

    public function show(Annonce $annonce)
    {
        // Chargement des segments liés avec les livreurs
        $annonce->load('segments.livreur');

        return view('delivery.annonces.show', compact('annonce'));
    }
}
