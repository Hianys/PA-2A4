<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Client\AnnonceController as CLientController;
use App\Http\Controllers\Provider\AnnonceController as ProviderController;
//use App\Http\Controllers\UserController;

// Authentification
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// Annonces (Livraisons)
Route::get('/annonces/transport', [CLientController::class, 'indexTransport']);
Route::post('/annonces/transport/{id}/valider', [CLientController::class, 'validerTransport']);

// Prestations
Route::get('/annonces/prestation', [ProviderController::class, 'indexPrestation']);
Route::post('/annonces/prestation/{id}/valider', [ProviderController::class, 'validerPrestation']);

