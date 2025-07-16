<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;

class WalletService
{
    public function credit(User $user, float $amount, string $description = null)
    {
        $wallet = $user->wallet;
        $wallet->balance += $amount;
        $wallet->save();

        $wallet->transactions()->create([
            'type' => 'recharge',
            'amount' => $amount,
            'status' => 'success',
            'description' => $description,
        ]);
    }

    public function debit(User $user, float $amount, string $description = null)
    {
        $wallet = $user->wallet;

        if ($wallet->balance < $amount) {
            throw new \Exception("Insufficient wallet balance.");
        }

        $wallet->balance -= $amount;
        $wallet->save();

        $wallet->transactions()->create([
            'type' => 'debit',
            'amount' => $amount,
            'status' => 'success',
            'description' => $description,
        ]);
    }

    public function block(User $user, float $amount, string $description = null)
    {
        $wallet = $user->wallet;

        if ($wallet->balance < $amount) {
            throw new \Exception("Insufficient wallet balance to block.");
        }

        $wallet->balance -= $amount;
        $wallet->blocked_balance += $amount;
        $wallet->save();

        $wallet->transactions()->create([
            'type' => 'block',
            'amount' => $amount,
            'status' => 'success',
            'description' => $description,
        ]);
    }

    public function unblockTo(User $client, User $receiver, float $amount, string $description = null)
    {
        $walletClient = $client->wallet;
        $walletReceiver = $receiver->wallet;

        if ($walletClient->blocked_balance < $amount) {
            throw new \Exception("Insufficient blocked funds.");
        }

        $walletClient->blocked_balance -= $amount;
        $walletClient->save();

        $walletReceiver->balance += $amount;
        $walletReceiver->save();

        $walletClient->transactions()->create([
            'type' => 'unblock',
            'amount' => $amount,
            'status' => 'success',
            'description' => $description,
        ]);

        $walletReceiver->transactions()->create([
            'type' => 'credit_from_unblock',
            'amount' => $amount,
            'status' => 'success',
            'description' => $description,
        ]);
    }
}
