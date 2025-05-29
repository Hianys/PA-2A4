<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Annonce extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'from_city',
        'to_city',
        'from_lat',
        'from_lng',
        'to_lat',
        'to_lng',
        'preferred_date',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function segments()
    {
        return $this->hasMany(TransportSegment::class);
    }
}
