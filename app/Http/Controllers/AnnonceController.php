<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnonceController extends Controller
{
    public function index()
    {
        $annonces = auth()->user()->annonces()->latest()->get();

        return view('client.annonces.index', compact('annonces'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:transport,service',
            'preferred_date' => 'nullable|date',
            'from_city' => 'nullable|string|max:255',
            'to_city' => 'nullable|string|max:255',
        ]);

        Annonce::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'preferred_date' => $request->preferred_date,
            'from_city' => $request->from_city,
            'to_city' => $request->to_city,
        ]);

        return redirect()->route('client.annonces.index')->with('success', 'Annonce créée avec succès.');
    }

    public function show(Annonce $annonce)
    {
        if ($annonce->user_id !== auth()->id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        return view('client.annonces.show', compact('annonce'));
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

