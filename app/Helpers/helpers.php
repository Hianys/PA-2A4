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
