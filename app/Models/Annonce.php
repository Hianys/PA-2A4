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
        'preferred_date',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
