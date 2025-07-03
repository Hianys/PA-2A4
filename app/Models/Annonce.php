<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Annonce extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'type',
        'from_city',
        'to_city',
        'preferred_date',
        'time',
        'description',
        'from_lat',
        'from_lng',
        'to_lat',
        'to_lng',
        'price',
        'weight',
        'volume',
        'photo',
        'constraints',
        'status',
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
