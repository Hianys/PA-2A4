<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Annonce;



class AnnonceController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isTrader() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $annonces = auth()->user()->annonces()->latest()->get();
        return view('trader.annonces.index', compact('annonces'));
    }

    public function create()
    {
        if (!auth()->user()->isTrader() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('trader.annonces.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isTrader() && !auth()->user()->isAdmin()) {
            abort(403);
        }

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

        return redirect()->route('commercant.annonces.index')->with('success', 'Annonce créée avec succès.');
    }

    public function show(Annonce $annonce)
    {
        if (
            !auth()->user()->isTrader() && !auth()->user()->isAdmin()
            || (!auth()->user()->isAdmin() && $annonce->user_id !== auth()->id())
        ) {
            abort(403);
        }

        return view('trader.annonces.show', compact('annonce'));
    }

    public function edit(Annonce $annonce)
    {
        if (
            !auth()->user()->isTrader() && !auth()->user()->isAdmin()
            || (!auth()->user()->isAdmin() && $annonce->user_id !== auth()->id())
        ) {
            abort(403);
        }

        return view('trader.annonces.edit', compact('annonce'));
    }

    public function update(Request $request, Annonce $annonce)
    {
        if (
            !auth()->user()->isTrader() && !auth()->user()->isAdmin()
            || (!auth()->user()->isAdmin() && $annonce->user_id !== auth()->id())
        ) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:transport,service',
            'preferred_date' => 'nullable|date',
            'from_city' => 'nullable|string|max:255',
            'to_city' => 'nullable|string|max:255',
        ]);

        $annonce->update($request->only([
            'title', 'description', 'type', 'preferred_date', 'from_city', 'to_city'
        ]));

        return redirect()->route('commercant.annonces.show', $annonce)->with('success', 'Annonce mise à jour.');
    }

    public function destroy(Annonce $annonce)
    {
        if (
            !auth()->user()->isTrader() && !auth()->user()->isAdmin()
            || (!auth()->user()->isAdmin() && $annonce->user_id !== auth()->id())
        ) {
            abort(403);
        }

        $annonce->delete();
        return redirect()->route('commercant.annonces.index')->with('success', 'Annonce supprimée.');
    }
}
