<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportSegment extends Model
{
    protected $fillable = [
        'annonce_id',
        'delivery_id',
        'from_city',
        'to_city',
        'from_lat',
        'from_lng',
        'to_lat',
        'to_lng',
    ];

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    public function delivery()
    {
        return $this->belongsTo(User::class, 'delivery_id');
    }
}
