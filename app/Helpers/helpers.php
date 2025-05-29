<?php

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;


function dashboard_route_for(string $role): string
{
    return match ($role) {
        'client' => route('client.dashboard'),
        'livreur' => route('delivery.dashboard'),
        'commercant' => route('trader.dashboard'),
        'prestataire' => route('provider.dashboard'),
        'admin' => route('admin.dashboard'),
        default => route('home'),
    };
}
function getCoordinates($city)
{
    $response = Http::get('https://api-adresse.data.gouv.fr/search/', [
        'q' => $city,
        'limit' => 1,
    ]);

    if ($response->successful() && isset($response['features'][0])) {
        return $response['features'][0]['geometry']['coordinates']; // [lng, lat]
    }

    return [null, null];
}

