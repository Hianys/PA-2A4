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
            'status' => 'nullable|in:publiée,accepté,refusé',
            'from_latitude' => 'nullable|numeric',
            'from_longitude' => 'nullable|numeric',
            'to_latitude' => 'nullable|numeric',
            'to_longitude' => 'nullable|numeric',
            'photo' => 'nullable|file|image|max:2048',
        ]);

    $data = $validated;
    unset($data['photo']);
    $data['user_id'] = auth()->id();
    $data['status'] = 'publiée';

    foreach (['from_lat', 'from_lng', 'to_lat', 'to_lng', 'price', 'weight', 'volume'] as $field) {
            if (empty($data[$field])) {
                $data[$field] = null;
            }
        }

    if ($request->hasFile('photo')) {
        $path = $request->file('photo')->store('uploads', 'public');
        $data['photo'] = $path;
    } else {
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
            'status' => 'nullable|in:publiée,taken,completed',
            'from_latitude' => 'nullable|numeric',
            'from_longitude' => 'nullable|numeric',
            'to_latitude' => 'nullable|numeric',
            'to_longitude' => 'nullable|numeric',
            'photo' => 'nullable|file|image|max:2048',
        ]);

    $data = $validated;
    $data['user_id'] = auth()->id();

    unset($data['photo']);

    foreach (['from_lat', 'from_lng', 'to_lat', 'to_lng', 'price', 'weight', 'volume'] as $field) {
        if (empty($data[$field])) {
            $data[$field] = null;
        }
    }

    if ($request->hasFile('photo')) {
        $path = $request->file('photo')->store('uploads', 'public');
        $data['photo'] = $path;
    } else {
        $data['photo'] = $annonce->photo;
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

    //METHODES API APPLI MOBILE

    public function indexTransport(Request $request)
    {
        $userId = $request->input('user_id');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'user_id manquant dans la requête.'
            ], 400);
        }

        $annonces = \App\Models\Annonce::where('type', 'transport')
            ->where('user_id', $userId)
            ->get([
                'id',
                'title',
                'description',
                'from_city',
                'to_city',
                'preferred_date',
                'price',
                'status'
            ]);

        return response()->json([
            'success' => true,
            'annonces' => $annonces
        ]);
    }

    /*public function showTransport($id, Request $request)
    {
        $userId = $request->input('user_id');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'user_id manquant dans la requête.'
            ], 400);
        }

        $annonce = \App\Models\Annonce::where('id', $id)
            ->where('user_id', $userId)
            ->where('type', 'transport')
            ->first();

        if (!$annonce) {
            return response()->json([
                'success' => false,
                'message' => 'Annonce non trouvée ou non autorisée.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'annonce' => $annonce
        ]);
    }*/

    public function validerTransport($id, Request $request)
    {
        $userId = $request->input('user_id');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'user_id manquant dans la requête.'
            ], 400);
        }

        $annonce = \App\Models\Annonce::where('id', $id)
            ->where('user_id', $userId)
            ->where('type', 'transport')
            ->first();

        if (!$annonce) {
            return response()->json([
                'success' => false,
                'message' => 'Annonce non trouvée ou non autorisée.'
            ], 404);
        }

        // Mettre à jour le statut
        $annonce->status = 'complétée';
        $annonce->save();

        return response()->json([
            'success' => true,
            'message' => 'Annonce validée avec succès.',
            'annonce' => $annonce
        ]);
    }

}
