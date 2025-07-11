<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class WalletController extends Controller
{
    public function index()
    {
        return view('wallet.index', [
            'wallet' => Auth::user()->wallet,
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        // On enregistre le montant dans la session
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

    public function success(Request $request)
    {
        $amount = session('wallet_recharge_amount');

        if ($amount && $amount > 0) {
            $user = Auth::user();
            $user->wallet += $amount;
            $user->save();

            session()->forget('wallet_recharge_amount');
        }

        return view('wallet.success');
    }
}
