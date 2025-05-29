<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnnonceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransportSegmentController;

//Route de la page d'accueil
Route::get('/', function () {
    return view('home');
})->name('home');


//Routes des différents dashboards
Route::middleware(['auth'])->group(function () {
    Route::get('/client/dashboard', function () {
        return view('dashboards.client');
    })->name('client.dashboard');

    Route::get('/livreur/dashboard', function () {
        return view('dashboards.delivery');
    })->name('delivery.dashboard');

    Route::get('/commercant/dashboard', function () {
        return view('dashboards.trader');
    })->name('trader.dashboard');

    Route::get('/prestataire/dashboard', function () {
        return view('dashboards.provider');
    })->name('provider.dashboard');

    Route::get('/admin/dashboard/', [AdminController::class, 'index'])
        ->name('admin.dashboard');

});

//Actions utilisateurs admin
Route::middleware(['auth'])->group(function () {
    Route::patch('/admin/users/{id}/promote', [AdminController::class, 'promote'])->name('admin.promote');
    Route::patch('/admin/users/{id}/demote', [AdminController::class, 'demote'])->name('admin.demote');
    Route::delete('/admin/users/{id}', [AdminController::class, 'destroy'])->name('admin.delete');
});

//Gestion des annonces pour les clients
Route::middleware(['auth'])->group(function () {
    Route::get('/client/annonces', [AnnonceController::class, 'index'])->name('client.annonces.index');
    Route::get('/client/annonces/{annonce}', [AnnonceController::class, 'show'])->name('client.annonces.show');
    Route::post('/client/annonces', [AnnonceController::class, 'store'])->name('client.annonces.store');
    Route::put('/client/annonces/{annonce}', [AnnonceController::class, 'update'])->name('client.annonces.update');
    Route::delete('/client/annonces/{annonce}', [AnnonceController::class, 'destroy'])->name('client.annonces.destroy');
});

//Prise en charge des annonces de type transport pour les Livreurs
Route::middleware('auth')->group(function () {
    Route::get('/livreur/annonces', [AnnonceController::class, 'index'])->name('delivery.annonces.index');
    Route::get('/livreur/annonces/{annonce}', [AnnonceController::class, 'show'])->name('delivery.annonces.show');
    Route::post('/livreur/annonces/{annonce}/segment', [TransportSegmentController::class, 'store'])->name('segments.store');
    Route::get('/livreur/mes-livraisons', [TransportSegmentController::class, 'mesLivraisons'])->name('delivery.segments.index');
    Route::patch('livreur/segments/{segment}/status', [TransportSegmentController::class, 'updateStatus'])->name('segments.updateStatus');

});

//Actions dans le profil de l'utilisateur
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



require __DIR__.'/auth.php';
