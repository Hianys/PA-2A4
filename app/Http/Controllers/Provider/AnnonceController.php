<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnonceController extends Controller
{
    public function dashboard()
{
    $user = Auth::user();

    if (!$user->isProvider() && !$user->isAdmin()) {
        abort(403);
    }

    // Les missions du prestaeuh
    $missions = Annonce::where('provider_id', $user->id)
        ->whereIn('status', ['prise en charge', 'complétée'])
        ->latest()
        ->get();

    // Calcul revenu du mois euh 
    $revenus = $missions
        ->where('status', 'complétée')
        ->whereBetween('preferred_date', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])
        ->sum('price');

    return view('dashboards.provider', compact('missions', 'revenus'));
}

    public function index()
{
    $user = Auth::user();

    if (!$user->isProvider() && !$user->isAdmin()) {
        abort(403);
    }

    // annonce serviceuh encore pas prise en chargeuh
    $annonces = Annonce::where('type', 'service')
        ->whereNull('provider_id')
        ->where('status', 'publiée')
        ->latest()
        ->get();

    return view('provider.annonces.index', compact('annonces'));
}

    public function create()
    {
        $user = Auth::user();
        if (!$user->isProvider() && !$user->isAdmin()) {
            abort(403);
        }

        return view('provider.annonces.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isProvider() && !$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'price' => 'nullable|numeric',
            'constraints' => 'nullable|string',
            'photo' => 'nullable|image',
        ]);

        $annonce = new Annonce($validated);
        $annonce->user_id = $user->id;
        $annonce->type = 'service';
        $annonce->status = 'publiée';

        if ($request->hasFile('photo')) {
            $annonce->photo = $request->file('photo')->store('annonces', 'public');
        }

        $annonce->save();

        return redirect()->route('provider.annonces.index')->with('success', 'Annonce créée avec succès.');
    }

    public function show(Annonce $annonce)
{
    $user = Auth::user();

    // que les serviceux
    if ($annonce->type !== 'service') {
        abort(403);
    }

    // deja prise en chargeuh donc invisibleuh
    if ($annonce->status === 'prise en charge' && $annonce->provider_id !== $user->id) {
        abort(403);
    }

    return view('provider.annonces.show', compact('annonce'));
}

    public function edit(Annonce $annonce)
    {
        $user = Auth::user();
        if ((!$user->isProvider() && !$user->isAdmin()) || $annonce->user_id !== $user->id) {
            abort(403);
        }

        return view('provider.annonces.edit', compact('annonce'));
    }

    public function update(Request $request, Annonce $annonce)
    {
        $user = Auth::user();
        if ((!$user->isProvider() && !$user->isAdmin()) || $annonce->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'preferred_date' => 'required|date',
            'price' => 'nullable|numeric',
            'constraints' => 'nullable|string',
        ]);

        $annonce->update($validated);

        return redirect()->route('provider.annonces.index')->with('success', 'Annonce mise à jour avec succès.');
    }

    public function destroy(Annonce $annonce)
    {
        $user = Auth::user();
        if ((!$user->isProvider() && !$user->isAdmin()) || $annonce->user_id !== $user->id) {
            abort(403);
        }

        $annonce->delete();

        return redirect()->route('provider.annonces.index')->with('success', 'Annonce supprimée.');
    }

    public function markCompleted(Annonce $annonce)
    {
        $user = Auth::user();

        if ((!$user->isProvider() && !$user->isAdmin()) || $annonce->provider_id !== $user->id) {
            abort(403);
        }

        if ($annonce->status !== 'prise en charge') {
            return redirect()->back()->with('error', 'La mission n\'est pas en cours.');
        }

        // si besoins passe en attente de paieuhment
        if ($annonce->requires_payment_validation) {
            $annonce->status = 'en attente de paiement';
        } else {
            $annonce->status = 'complétée';
        }

        $annonce->save();

        return redirect()->route('provider.annonces.index')->with('success', 'Statut de la mission mis à jour.');
    }

    public function accept(Annonce $annonce)
{
    $user = Auth::user();

    // Vérifie de l'annonceuuuuuuuuuuuuh
    if ($annonce->type !== 'service' || $annonce->status !== 'publiée') {
        return redirect()->back()->with('error', 'Cette annonce ne peut pas être acceptée.');
    }

   
    if ($annonce->provider_id !== null) {
        return redirect()->back()->with('error', 'Cette annonce a déjà été acceptée par un autre prestataire.');
    }

    // association annonce au presta connectéeuh
    $annonce->provider_id = $user->id;
    $annonce->status = 'prise en charge';   
    $annonce->save();

    return redirect()->route('provider.dashboard')->with('success', 'Annonce acceptée avec succès.');
}

    public function missions()
{
    $user = Auth::user();

    if (!$user->isProvider() && !$user->isAdmin()) {
        abort(403);
    }

    // toute nos missions
    $annonces = Annonce::where('provider_id', $user->id)
        ->whereIn('status', ['prise en charge', 'complétée'])
        ->latest()
        ->get();

    return view('provider.annonces.missions', compact('annonces'));
}
    
        public function confirmerAnnonce(Request $request, Annonce $annonce)
{
    $prestataire = $annonce->acceptedBy;
    $amount = $annonce->price;

    if ($prestataire->wallet->blocked_balance < $amount) {
        return back()->with('error', 'Fonds insuffisants.');
    }

    // debloqueuh puis transfereuh (sur le compte sinon ca reste dans bloqué lol)
    $prestataire->wallet->blocked_balance -= $amount;
    $prestataire->wallet->balance += $amount;
    $prestataire->wallet->save();

    $annonce->is_confirmed = true;
    $annonce->save();

    $transaction = $prestataire->wallet->transactions()
        ->where('type', 'service')
        ->where('status', 'pending')
        ->latest()
        ->first();

    if ($transaction) {
        $transaction->status = 'completed';
        $transaction->save();
    }

    return back()->with('success', 'Service confirmé. Paiement libéré.');
}
}
