<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use Illuminate\Http\Request;

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
}

