<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use Illuminate\Http\Request;
use App\Services\WalletService;
use Illuminate\Support\Facades\Http;

class AnnonceController extends Controller
{

    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }
    public function index()
    {
        if (!(auth()->user()->isClient() || auth()->user()->isAdmin())) {
            abort(403);
        }

        $annonces = auth()->user()
            ->annonces()
            ->where('status', '!=', 'archivée')
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
            'from_lat' => 'nullable|numeric',
            'from_lng' => 'nullable|numeric',
            'to_lat' => 'nullable|numeric',
            'to_lng' => 'nullable|numeric',
            'price' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'volume' => 'nullable|numeric',
            'constraints' => 'nullable|string',
            'status' => 'nullable|in:published,taken,completed',
            'photo' => 'nullable|file|image|max:2048',
        ]);

        $data = $validated;
        $data['user_id'] = auth()->id();
        $data['status'] = 'publiée';

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('uploads', 'public');
            $data['photo'] = $path;
        }

        Annonce::create($data);

        return redirect()->route('client.annonces.index')->with('success', 'Annonce créée avec succès.');
    }

    public function show(Annonce $annonce)
    {
        if (
            !(auth()->user()->isClient() || auth()->user()->isAdmin()) ||
            (!auth()->user()->isAdmin() && $annonce->user_id !== auth()->id())
        ) {
            abort(403);
        }

        if ($annonce->status === 'archivée') {
            abort(404, 'Cette annonce est archivée.');
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
            'photo' => 'nullable|file|image|max:2048',
        ]);

        $data = $validated;

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('uploads', 'public');
            $data['photo'] = $path;
        }

        $annonce->update($data);

        return redirect()->route('client.annonces.show', $annonce)->with('success', 'Annonce mise à jour.');
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
        return redirect()->route('client.annonces.index')->with('success', 'Annonce supprimée.');
    }

    public function confirmDelivery(Request $request, \App\Models\Annonce $delivery)
    {
        $client = auth()->user();

        // récupère le livreur lié à l’annonce
        $livreur = $delivery->livreur;
        if (!$livreur) {
            return redirect()
                ->route('client.annonces.show', $delivery)
                ->with('error', 'Aucun livreur associé à cette annonce.');
        }

        $this->walletService->unblockTo(
            $client,
            $livreur,
            $delivery->price,
            "Déblocage livraison #{$delivery->id}"
        );

        $delivery->update([
            'status' => 'complétée'
        ]);

        return redirect()
            ->route('client.annonces.show', $delivery)
            ->with('success', 'Livraison confirmée et paiement débloqué au livreur.');
    }



    public function payDelivery(Request $request, \App\Models\Annonce $delivery)
    {
        $user = auth()->user();
        $amount = $delivery->price;

        $wallet = $user->wallet;

        if ($wallet && $wallet->balance >= $amount) {
            $this->walletService->block($user, $amount, "Paiement livraison #{$delivery->id}");

            $delivery->update([
                'status' => 'bloqué'
            ]);

            return redirect()
                ->route('client.annonces.show', $delivery)
                ->with('success', 'Livraison payée et argent bloqué.');
        } else {
            return redirect()
                ->route('wallet.index')
                ->with('error', 'Solde insuffisant. Veuillez recharger votre portefeuille.');
        }
    }
}
