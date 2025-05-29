<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnonceController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'livreur') {
            // Annonces à prendre en charge
            $annonces = Annonce::where('type', 'transport')
                ->latest()
                ->with('segments')
                ->get();

            return view('delivery.annonces.index', compact('annonces'));
        }

        if ($user->role === 'client') {
            // Ses propres annonces
            $annonces = $user->annonces()->latest()->get();

            return view('client.annonces.index', compact('annonces'));
        }

        if ($user->role === 'admin') {
            // Toutes les annonces
            $annonces = Annonce::latest()->with('user')->get();

            return view('admin.annonces.index', compact('annonces'));
        }

        abort(403);
    }



    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:transport,service',
            'preferred_date' => 'required|date|after_or_equal:today',
            'from_city' => 'required_if:type,transport|string|max:255',
            'to_city' => 'required_if:type,transport|string|max:255',
            ],
            [
                'preferred_date.after_or_equal' => 'La date souhaitée ne peut pas être antérieure à aujourd’hui.',
                'from_city.required_if' => 'La ville de départ est obligatoire pour un transport.',
                'to_city.required_if' => 'La ville d’arrivée est obligatoire pour un transport.',
        ]);

        // Initialisation des coordonnées
        $fromLat = $fromLng = $toLat = $toLng = null;

        if ($request->type === 'transport') {
            [$fromLng, $fromLat] = getCoordinates($request->from_city);
            [$toLng, $toLat] = getCoordinates($request->to_city);
        }

        Annonce::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'preferred_date' => $request->preferred_date,
            'from_city' => $request->from_city,
            'to_city' => $request->to_city,
            'from_lat' => $fromLat,
            'from_lng' => $fromLng,
            'to_lat' => $toLat,
            'to_lng' => $toLng,
        ]);

        return redirect()->route('client.annonces.index')->with('success', 'Annonce créée avec succès.');
    }

    public function show(Annonce $annonce)
    {
        $user = auth()->user();

        // Cas client : accès uniquement à ses propres annonces
        if ($user->role === 'client' && $annonce->user_id !== $user->id) {
            abort(403);
        }

        // Cas livreur : peut voir toutes les annonces de type transport
        if ($user->role === 'livreur' && $annonce->type !== 'transport') {
            abort(403);
        }

        // Cas admin : accès total (pas de restriction)
        // Autres rôles → bloqués
        if (!in_array($user->role, ['client', 'livreur', 'admin'])) {
            abort(403);
        }

        // Préchargement des segments
        $annonce->load('segments.delivery');

        // Vue différente selon le rôle
        $view = match ($user->role) {
            'client' => 'client.annonces.show',
            'livreur' => 'delivery.annonces.show',
            default => 'client.annonces.show' // fallback admin
        };

        return view($view, [
            'annonce' => $annonce,
            'segments' => $annonce->segments,
        ]);
    }


    public function update(Request $request, Annonce $annonce)
    {
        // Empêche la modification par un autre utilisateur
        if ($annonce->user_id !== auth()->id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'from_city' => 'nullable|string|max:255',
            'to_city' => 'nullable|string|max:255',
            'preferred_date' => 'nullable|date',
        ]);

        $annonce->update($request->only([
            'title', 'description', 'from_city', 'to_city', 'preferred_date'
        ]));

        return redirect()->route('client.annonces.show', $annonce)->with('success', 'Annonce mise à jour.');
    }

    public function destroy(Annonce $annonce)
    {
        if ($annonce->user_id !== auth()->id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $annonce->delete();

        return redirect()->route('client.annonces.index')->with('success', 'Annonce supprimée.');
    }


}

