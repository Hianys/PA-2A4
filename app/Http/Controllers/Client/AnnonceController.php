<?php
namespace App\Http\Controllers\Client;

    use Illuminate\Support\Facades\Log;
    use App\Http\Controllers\Controller;
    use App\Models\Annonce;
    use Illuminate\Http\Request;
    use App\Services\WalletService;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Http;
    use App\Services\PaymentService;

    class AnnonceController extends Controller
    {

        protected $walletService;
    protected $paymentService;

        public function __construct(WalletService $walletService, PaymentService $paymentService)
    {
        $this->walletService = $walletService;
        $this->paymentService = $paymentService;
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

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:transport,service',
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'price' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'volume' => 'nullable|numeric',
            'constraints' => 'nullable|string',
            'status' => 'nullable|in:publiée,prise en charge,completée',
            'photo' => 'nullable|file|image|max:2048',
        ];

        if ($request->input('type') === 'transport') {
            $rules['from_city'] = 'required|string|max:255';
            $rules['to_city'] = 'required|string|max:255';
            $rules['from_lat'] = 'required|numeric';
            $rules['from_lng'] = 'required|numeric';
            $rules['to_lat'] = 'required|numeric';
            $rules['to_lng'] = 'required|numeric';
        }

        $validated = $request->validate($rules);

        // quand c'est videuh ca devient nulleuh
        foreach (['weight', 'volume', 'from_lat', 'from_lng', 'to_lat', 'to_lng'] as $field) {
            if (array_key_exists($field, $validated) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        $data = $validated;
        unset($data['photo']);
        $data['user_id'] = auth()->id();
        $data['status'] = 'publiée';

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

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:transport,service',
            'preferred_date' => 'nullable|date',
            'price' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'volume' => 'nullable|numeric',
            'constraints' => 'nullable|string',
            'status' => 'nullable|in:publiée,prise en charge,completée',
            'photo' => 'nullable|file|image|max:2048',
        ];

        if ($request->input('type') === 'transport') {
            $rules['from_city'] = 'required|string|max:255';
            $rules['to_city'] = 'required|string|max:255';
            $rules['from_lat'] = 'required|numeric';
            $rules['from_lng'] = 'required|numeric';
            $rules['to_lat'] = 'required|numeric';
            $rules['to_lng'] = 'required|numeric';
        }

        $validated = $request->validate($rules);

       
        foreach (['weight', 'volume', 'from_lat', 'from_lng', 'to_lat', 'to_lng'] as $field) {
            if (array_key_exists($field, $validated) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        $data = $validated;
        $data['user_id'] = auth()->id();

        unset($data['photo']);

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

        

    public function confirmDelivery(Request $request, Annonce $annonce)
{
    $client = auth()->user();

    //verif si le client est celui de l'annonceuh
    if (
        $annonce->user_id !== $client->id ||
        $annonce->status !== 'prise en charge' ||
        !$annonce->provider
    ) {
        abort(403, 'Action non autorisée.');
    }

    try {
        Log::info('==> Début paiement prestataire');

        // Paiement immédiat
        $this->paymentService->payProviders($annonce);

        // MAJ statut de l'annonceuh
        $annonce->status = 'complétée';
        $annonce->save();

        Log::info('==> Annonce complétée avec succès');

        return redirect()->route('client.annonces.show', $annonce)
            ->with('success', 'Service marqué comme complété et prestataire payé.');
    } catch (\Exception $e) {
        Log::error('Erreur confirmDelivery : ' . $e->getMessage());

        if (str_contains($e->getMessage(), 'Solde insuffisant')) {
            return back()->with('insufficient_balance', true);
        }

        return back()->with('error', 'Une erreur est survenue lors du paiement.');
    }
}


        public function payDelivery($id)
    {
        $annonce = \App\Models\Annonce::findOrFail($id);

        if ($annonce->status !== 'en attente de paiement') {
            return back()->with('error', 'Cette annonce n’est pas en attente de paiement.');
        }

        $annonce->status = 'complétée';
        $annonce->save();

        return back()->with('success', 'Service payé. Mission complétée.');
    }

    public function validateTransportAnnonce(Request $request, Annonce $annonce)
    {
        $client = auth()->user();

        if (
            $annonce->user_id !== $client->id ||
            $annonce->type !== 'transport'
        ) {
            abort(403, 'Action non autorisée.');
        }

        try {
            // accepteuh tout
            $annonce->segments()->where('status', 'en attente')->update([
                'status' => 'accepté',
            ]);

            // nouveau statut pour une nouvelle vieuh
            $annonce->status = 'prise en charge';
            $annonce->save();

            return redirect()->route('client.annonces.show', $annonce)
                ->with('success', 'Segments validés. La livraison est maintenant en cours.');
        } catch (\Exception $e) {
            \Log::error('Erreur validateTransportAnnonce : ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la validation des segments.');
        }
    }

    public function marquerEnAttenteDePaiement(Annonce $annonce)
    {
        $user = auth()->user();

        if (!$user->isDelivery()) {
            abort(403);
        }

        // verif de liaison entre livreur et annonceuh au moins via segments
        $livreurId = $user->id;
        $livreurSegments = $annonce->segments()->where('delivery_id', $livreurId)->count();

        if ($livreurSegments === 0) {
            return back()->with('error', 'Vous ne participez pas à cette annonce.');
        }

        // Changement de statut
        $annonce->status = 'en attente de paiement';
        $annonce->save();

        return back()->with('success', 'Annonce marquée en attente de paiement.');
    }


    public function confirmTransportDelivery(Annonce $annonce)
{
    Log::info('[ Début] confirmTransportDelivery pour annonce ID: ' . $annonce->id);

    if (auth()->id() !== $annonce->user_id) {
        Log::warning('[ Refusé] Mauvais utilisateur pour confirmer annonce ID: ' . $annonce->id);
        abort(403, 'Vous n’êtes pas autorisé à confirmer cette annonce.');
    }

    // Vérifi statut/type
    if ($annonce->type !== 'transport' || $annonce->status !== 'en attente de paiement') {
        Log::warning('[ Refusé] Mauvais statut ou type pour annonce ID: ' . $annonce->id);
        return back()->with('error', "L’annonce n’est pas prête pour être confirmée.");
    }

    // Chargement des segments
    $annonce->load('segments');
    Log::info('[ Segments chargés] Nb segments: ' . $annonce->segments->count());

    // verif statit segmenteuh
    if ($annonce->segments->isEmpty() || !$annonce->segments->every(fn($s) => $s->status === 'en attente de paiement')) {
        Log::warning('[ Bloqué] Tous les segments ne sont pas en attente de paiement');
        return back()->with('error', "Tous les segments doivent être en attente de paiement pour confirmer.");
    }

    try {
        // Paiement via serviceuh 
        Log::info('[ Paiement] Lancement de processSegmentedPayment...');
        app(PaymentService::class)->processSegmentedPayment($annonce);
        Log::info('[ Paiement OK]');

        // Marquer les segments comme "accepté"
        foreach ($annonce->segments as $segment) {
            $segment->status = 'acceptée';
            $segment->save();
        }
        Log::info('[ Segments acceptés]');

        //MAJ annonce
        $annonce->status = 'complétée';
        $annonce->is_paid = true;
        $annonce->is_confirmed = true;
        $annonce->save();
        Log::info('[ Annonce mise à jour] complétée + payée');

        return redirect()->route('client.annonces.show', $annonce)
            ->with('success', "Paiement effectué et livraison confirmée.");
    } catch (\Exception $e) {
        Log::error('[ Exception] ' . $e->getMessage());
        return back()->with('error', "Erreur : " . $e->getMessage());
    }
}

public function markAsAwaitingPayment(Annonce $annonce)
{
    $user = auth()->user();

    if (!$user->isClient() || $annonce->user_id !== $user->id || $annonce->type !== 'transport') {
        abort(403, 'Action non autorisée.');
    }

    try {
        \Log::info("[ Client] Changement vers 'en attente de paiement' pour annonce ID: " . $annonce->id);

        // MAJ des segments
        $annonce->segments()->update(['status' => 'en attente de paiement']);

        // MAJ de l’annonce
        $annonce->status = 'en attente de paiement';
        $annonce->save();

        return redirect()->route('client.annonces.show', $annonce)
            ->with('success', 'Annonce et segments passés en attente de paiement.');
    } catch (\Exception $e) {
        \Log::error("[ Erreur markAsAwaitingPayment] " . $e->getMessage());
        return back()->with('error', 'Erreur lors de la mise à jour.');
    }
}
    }