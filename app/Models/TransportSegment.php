<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportSegment extends Model
{
    protected $fillable = ['annonce_id', 'livreur_id', 'from_city', 'to_city', 'status'];

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    public function livreur()
    {
        return $this->belongsTo(User::class, 'livreur_id');
    }
}
