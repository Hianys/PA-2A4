<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\TransportSegment;
use Illuminate\Http\Request;

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
    //  Bloque la création si le trajet est déjà couvert
    if (!$annonce->canReceiveSegment()) {
        return back()->with('error', 'Le trajet est déjà couvert. Aucun nouveau segment ne peut être proposé.');
    }

    // Validation des champs
    $request->validate([
        'from_city' => 'required|string|max:255',
        'to_city' => 'required|string|max:255',
    ]);

    // Vérification simple
    if ($request->from_city === $request->to_city) {
        return back()->with('error', 'La ville de départ et d’arrivée doivent être différentes.');
    }

    //  Coordonnées
    [$fromLng, $fromLat] = getCoordinates($request->from_city);
    [$toLng, $toLat] = getCoordinates($request->to_city);

    //  Création du segment
    TransportSegment::create([
        'annonce_id' => $annonce->id,
        'delivery_id' => auth()->id(),
        'from_city' => $request->from_city,
        'to_city' => $request->to_city,
        'from_lat' => $fromLat,
        'from_lng' => $fromLng,
        'to_lat' => $toLat,
        'to_lng' => $toLng,
        'status' => 'en attente',
    ]);

    return back()->with('success', 'Segment proposé avec succès.');
}

    public function accept(TransportSegment $segment)
{
    if (!auth()->user()->isClient() && !auth()->user()->isAdmin() && !auth()->user()->isTrader()) {
        abort(403);
    }

    // Ajout du livreur + statut
    $segment->update([
        'delivery_id' => auth()->id(),
        'status' => 'accepté'
    ]);

    $annonce = $segment->annonce;

    // Vérifie si TOUS les segments sont acceptés par le même livreur
    $segments = $annonce->segments;

    $allSameLivreur = $segments->every(function ($seg) {
        return $seg->delivery_id !== null && $seg->delivery_id === auth()->id() && $seg->status === 'accepté';
    });

    // Vérifie que le trajet est bien complet
    $acceptedSegments = $segments->where('status', 'accepté');
    $hasStart = $acceptedSegments->contains('from_city', $annonce->from_city);
    $hasEnd = $acceptedSegments->contains('to_city', $annonce->to_city);
    $noPending = $segments->where('status', 'en attente')->count() === 0;

    if ($allSameLivreur && $hasStart && $hasEnd && $noPending) {
        $annonce->update([
            'status' => 'prise en charge',
            'livreur_id' => auth()->id(),
        ]);
    }

    return back()->with('success', 'Segment accepté.');
}


    public function refuse(TransportSegment $segment)
    {
        if (!auth()->user()->isClient() && !auth()->user()->isAdmin() && !auth()->user()->isTrader()) {
            abort(403);
        }

        $segment->update(['status' => 'refusé']);

        return back()->with('success', 'Segment refusé.');
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
            'status' => 'required|in:en attente,accepte,refuse'
        ]);

        $segment->update(['status' => $request->status]);

        return back()->with('success', 'Statut mis à jour avec succès.');
    }

    private function trajetEstComplet($from, $to, $segments)
{
    $graph = [];

    foreach ($segments as $segment) {
        $graph[$segment->from_city][] = $segment->to_city;
    }

    // Parcours en profondeur (DFS) pour vérifier si from → to est possible
    $visited = [];

    $stack = [$from];

    while (!empty($stack)) {
        $current = array_pop($stack);

        if ($current === $to) {
            return true;
        }

        if (isset($visited[$current])) {
            continue;
        }

        $visited[$current] = true;

        if (isset($graph[$current])) {
            foreach ($graph[$current] as $neighbor) {
                $stack[] = $neighbor;
            }
        }
    }

    return false;
}

public function markAsDone(Request $request, TransportSegment $segment)
{
    $user = auth()->user();

    if ($segment->delivery_id !== $user->id) {
        return back()->with('error', 'Vous n\'êtes pas autorisé à valider ce segment.');
    }

    $segment->status = 'réalisé';
    $segment->save();

    // Vérifier si TOUS les segments sont réalisés
    $annonce = $segment->annonce;
    $tousRealises = $annonce->segments()->where('status', '!=', 'réalisé')->count() === 0;

    if ($tousRealises) {
        $annonce->status = 'en attente de paiement';
        $annonce->save();
    }

    return back()->with('success', 'Segment validé. Merci !');
}

    

}
