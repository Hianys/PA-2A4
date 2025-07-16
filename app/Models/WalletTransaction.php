<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'status',
        'related_order_id',
        'description'
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
