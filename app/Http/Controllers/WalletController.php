<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Services\WalletService;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function index()
    {
        $user = auth()->user();
        $wallet = $user->wallet;

        return view('wallet.index', [
            'wallet' => $wallet,
        ]);
    }


    public function checkout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        // sauvegarde du montant dans la session
        session(['wallet_recharge_amount' => $request->amount]);

        Stripe::setApiKey(config('services.stripe.secret'));

        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Recharge du portefeuille',
                    ],
                    'unit_amount' => $request->amount * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('wallet.success'),
            'cancel_url' => route('wallet.index'),
        ]);

        return redirect($checkoutSession->url);
    }

    public function withdraw(Request $request)
    {
        $user = auth()->user();

        // seul la bonne personne peut recup le soldeuh
        if (!in_array($user->role, ['livreur', 'prestataire'])) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à effectuer un retrait.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $amount = $request->amount;

        if ($user->wallet->balance < $amount) {
            return back()->with('error', 'Solde insuffisant pour ce retrait.');
        }

        // Débiter le wallet
        $user->wallet->balance -= $amount;
        $user->wallet->save();

        // Créer une transactiongi
        $user->wallet->transactions()->create([
            'type' => 'withdraw',
            'amount' => $amount,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Votre demande de retrait a été enregistrée. Un virement sera effectué sous peu.');
    }

    public function success(Request $request)
    {
        $amount = session('wallet_recharge_amount');

        if ($amount && $amount > 0) {
            $user = Auth::user();
            $this->walletService->credit($user, $amount, 'Stripe recharge');

            session()->forget('wallet_recharge_amount');
        }

        return view('wallet.success');
    }
}
