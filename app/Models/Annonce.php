<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Annonce extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'from_city',
        'to_city',
        'date',
        'time',
        'description',
        'from_latitude',
        'from_longitude',
        'to_latitude',
        'to_longitude',
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
