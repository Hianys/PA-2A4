<?php

namespace App\Services;

use App\Models\Annonce;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function processSegmentedPayment(Annonce $annonce)
    {
        $commercant = $annonce->user;
        $segments = $annonce->segments()->where('status', 'accepté')->get();

        if ($segments->isEmpty()) {
            throw new \Exception("Aucun segment accepté.");
        }

        $totalDistance = $annonce->getTotalDistance();

        if ($totalDistance == 0) {
            throw new \Exception("Distance totale nulle.");
        }

        $amount = $annonce->price;

        if ($commercant->wallet->balance < $amount) {
            throw new \Exception("Solde insuffisant.");
        }

        // Paiement proportionnel
        DB::transaction(function () use ($commercant, $segments, $totalDistance, $amount) {
            // Débiter le commerçant
            $commercant->wallet->balance -= $amount;
            $commercant->wallet->save();

            // Regrouper les segments par livreur
            $grouped = $segments->groupBy('delivery_id');

            foreach ($grouped as $livreurId => $livreurSegments) {
                $livreur = $livreurSegments->first()->delivery;

                // Distance totale assurée par ce livreur
                $livreurDistance = $livreurSegments->sum(function ($s) {
                    return $s->distance();
                });

                $part = $livreurDistance / $totalDistance;
                $livreurAmount = round($amount * $part, 2);

                // Créditer le compte bloqué
                $livreur->wallet->blocked_balance += $livreurAmount;
                $livreur->wallet->save();

                // Créer une transaction en attente
                $livreur->wallet->transactions()->create([
                    'type' => 'delivery',
                    'amount' => $livreurAmount,
                    'status' => 'pending',
                ]);
            }
        });
    }
}