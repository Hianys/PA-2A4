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
            'from_city' => 'required|string|max:255',
            'to_city' => 'required|string|max:255',
            'preferred_date' => 'required|date',
            'type' => 'required|in:transport,service',
        ]);

        auth()->user()->annonces()->create($request->all());

        return redirect()->route('client.annonces.index')->with('success', 'Annonce créée avec succès.');
    }
}

