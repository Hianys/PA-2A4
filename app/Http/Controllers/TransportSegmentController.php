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

        if ($request->from_city === $request->to_city) {
            return back()->with('error', 'La ville de départ et d’arrivée doivent être différentes.');
        }

        // Récupération des coordonnées avec ta fonction helper
        [$fromLng, $fromLat] = getCoordinates($request->from_city);
        [$toLng, $toLat] = getCoordinates($request->to_city);

        // Création du segment
        TransportSegment::create([
            'annonce_id' => $annonce->id,
            'delivery_id' => auth()->id(), // ← si renommé
            'from_city' => $request->from_city,
            'to_city' => $request->to_city,
            'from_lat' => $fromLat,
            'from_lng' => $fromLng,
            'to_lat' => $toLat,
            'to_lng' => $toLng,
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

    public function mesLivraisons()
    {
        $segments = \App\Models\TransportSegment::with('annonce')
            ->where('delivery_id', auth()->id())
            ->latest()
            ->get();

        return view('delivery.segments.index', compact('segments'));
    }

    public function updateStatus(Request $request, TransportSegment $segment)
    {
        if ($segment->delivery_id !== auth()->id() and auth()->role() !== 'admin') {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,delivered'
        ]);

        $segment->update(['status' => $request->status]);

        return back()->with('success', 'Statut mis à jour avec succès.');
    }


}
