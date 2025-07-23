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
        'status',
    ];

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    public function delivery()
    {
        return $this->belongsTo(User::class, 'delivery_id');
    }

    public function distance(): float
    {
        // Vérifie que toutes les coordonnées sont bien présentes
        if (
            is_null($this->from_lat) || is_null($this->from_lng) ||
            is_null($this->to_lat) || is_null($this->to_lng)
        ) {
            return 0;
        }

        // Rayon moyen de la Terre en kilomètres
        $earthRadius = 6371;

        // Conversion en radians
        $lat1 = deg2rad($this->from_lat);
        $lon1 = deg2rad($this->from_lng);
        $lat2 = deg2rad($this->to_lat);
        $lon2 = deg2rad($this->to_lng);

        // Formule de Haversine
        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;

        $a = sin($deltaLat / 2) ** 2 +
             cos($lat1) * cos($lat2) * sin($deltaLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2); // distance en km arrondie
    }
}
