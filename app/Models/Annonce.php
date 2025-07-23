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

    public function livreur()
    {
        return $this->belongsTo(User::class, 'livreur_id');
    }

    
public function canReceiveSegment(): bool
{
    // Segments acceptés
    $segments = $this->segments()->where('status', 'accepté')->get();

    // Vérifie si le trajet complet est déjà couvert
    $trajetComplet = $this->trajetEstComplet($this->from_city, $this->to_city, $segments);

    // Vérifie s’il existe encore des segments "en attente"
    $enAttente = $this->segments()->where('status', 'en attente')->exists();

    // On autorise de nouveaux segments seulement si le trajet N’EST PAS encore complet ou s’il reste des en attente
    return !$trajetComplet || $enAttente;
}

private function trajetEstComplet($from, $to, $segments)
{
    $graph = [];

    foreach ($segments as $segment) {
        $graph[$segment->from_city][] = $segment->to_city;
    }

    $visited = [];
    $stack = [$from];

    while (!empty($stack)) {
        $current = array_pop($stack);

        if ($current === $to) {
            return true;
        }

        if (isset($visited[$current])) {
            continue;
        }

        $visited[$current] = true;

        if (isset($graph[$current])) {
            foreach ($graph[$current] as $neighbor) {
                $stack[] = $neighbor;
            }
        }
    }

    return false;
}
    public function getTotalDistance(): float
    {
        return $this->segments->sum(function ($segment) {
            return $segment->distance();
        });
    }

}
