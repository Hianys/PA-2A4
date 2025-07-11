<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use Illuminate\Http\Request;

class AnnonceController extends Controller
{
   public function index()
    {
        if (!auth()->user()->isClient() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        // Récupère toutes les annonces créées par ce client avec leur prestataire
        $annonces = Annonce::with('provider')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('client.annonces.index', compact('annonces'));
    }

    public function create()
    {
        if (!(auth()->user()->isClient() || auth()->user()->isAdmin())) {
            abort(403);
        }

        return view('client.annonces.create');
    }

    public function store(Request $request)
    {
        if (!(auth()->user()->isClient() || auth()->user()->isAdmin())) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:transport,service',
            'preferred_date' => 'nullable|date',
            'from_city' => 'nullable|string|max:255',
            'to_city' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'volume' => 'nullable|numeric',
            'constraints' => 'nullable|string',
            'status' => 'nullable|in:published,taken,completed',
            'from_latitude' => 'nullable|numeric',
            'from_longitude' => 'nullable|numeric',
            'to_latitude' => 'nullable|numeric',
            'to_longitude' => 'nullable|numeric',
            'photo' => 'nullable|file|image|max:2048',
        ]);

        $data = $validated;
        unset($data['photo']);
        $data['user_id'] = auth()->id();
        $data['status'] = 'published';

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('uploads', 'public');
            $data['photo'] = $path;
        }else {
            $data['photo'] = null;
        }

        Annonce::create($data);

        return redirect()->route('client.annonces.index')
            ->with('success', 'Annonce créée avec succès.');
    }

    public function show(Annonce $annonce)
    {
        if (
            !(auth()->user()->isClient() || auth()->user()->isAdmin()) ||
            (!auth()->user()->isAdmin() && $annonce->user_id !== auth()->id())
        ) {
            abort(403);
        }

        $annonce->load('segments.delivery');
        $segments = $annonce->segments;

        return view('client.annonces.show', compact('annonce', 'segments'));
    }

    public function edit(Annonce $annonce)
    {
        if (
            !(auth()->user()->isClient() || auth()->user()->isAdmin()) ||
            (!auth()->user()->isAdmin() && $annonce->user_id !== auth()->id())
        ) {
            abort(403);
        }

        return view('client.annonces.edit', compact('annonce'));
    }

    public function update(Request $request, Annonce $annonce)
    {
        if (
            !(auth()->user()->isClient() || auth()->user()->isAdmin()) ||
            (!auth()->user()->isAdmin() && $annonce->user_id !== auth()->id())
        ) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:transport,service',
            'preferred_date' => 'nullable|date',
            'from_city' => 'nullable|string|max:255',
            'to_city' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'volume' => 'nullable|numeric',
            'constraints' => 'nullable|string',
            'status' => 'nullable|in:published,taken,completed',
            'from_latitude' => 'nullable|numeric',
            'from_longitude' => 'nullable|numeric',
            'to_latitude' => 'nullable|numeric',
            'to_longitude' => 'nullable|numeric',
            'photo' => 'nullable|file|image|max:2048',
        ]);

        $data = $validated;
        $data['user_id'] = auth()->id();

        unset($data['photo']);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('uploads', 'public');
            $data['photo'] = $path;
        } else {
            $data['photo'] = $annonce->photo; // conserve l'ancienne si pas de nouvelle
        }

        $annonce->update($data);

        return redirect()->route('client.annonces.show', $annonce)
            ->with('success', 'Annonce mise à jour avec succès.');
    }


    public function destroy(Annonce $annonce)
    {
        if (
            !(auth()->user()->isClient() || auth()->user()->isAdmin()) ||
            (!auth()->user()->isAdmin() && $annonce->user_id !== auth()->id())
        ) {
            abort(403);
        }

        $annonce->delete();

        return redirect()->route('client.annonces.index')
            ->with('success', 'Annonce supprimée.');
    }

    public function markCompleted(Annonce $annonce)
    {
        if ($annonce->user_id !== auth()->id()) {
            abort(403); // Sécurité : le client ne peut modifier que ses annonces
        }

        if ($annonce->status !== 'taken') {
            return redirect()->back()->with('error', "Cette annonce n'a pas encore été acceptée.");
        }

        $annonce->status = 'completed';
        $annonce->save();

        return redirect()->back()->with('success', 'Annonce marquée comme complétée.');
    }
}
