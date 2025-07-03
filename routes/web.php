<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Client\AnnonceController as ClientAnnonceController;
use App\Http\Controllers\Trader\AnnonceController as TraderAnnonceController;
use App\Http\Controllers\Delivery\AnnonceController as DeliveryAnnonceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransportSegmentController;
use Illuminate\Support\Facades\Route;

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
    Route::get('/client/annonces', [ClientAnnonceController::class, 'index'])->name('client.annonces.index'); 
    Route::get('/client/annonces/create', [ClientAnnonceController::class, 'create'])->name('client.annonces.create');
    Route::get('/client/annonces/{annonce}', [ClientAnnonceController::class, 'show'])->name('client.annonces.show');
    Route::get('/client/annonces/{annonce}/edit', [ClientAnnonceController::class, 'edit'])->name('client.annonces.edit');
    Route::post('/client/annonces', [ClientAnnonceController::class, 'store'])->name('client.annonces.store');
    Route::put('/client/annonces/{annonce}', [ClientAnnonceController::class, 'update'])->name('client.annonces.update');
    Route::delete('/client/annonces/{annonce}', [ClientAnnonceController::class, 'destroy'])->name('client.annonces.destroy');
});

//Prise en charge des annonces de type transport pour les Livreurs
Route::middleware('auth')->group(function () {
    Route::get('/livreur/annonces', [DeliveryAnnonceController::class, 'index'])->name('delivery.annonces.index');
    Route::get('/livreur/annonces/{annonce}', [DeliveryAnnonceController::class, 'show'])->name('delivery.annonces.show');
    Route::post('/livreur/annonces/{annonce}/segment', [TransportSegmentController::class, 'store'])->name('segments.store');
    Route::get('/livreur/mes-livraisons', [TransportSegmentController::class, 'mesLivraisons'])->name('delivery.segments.index');
    Route::patch('livreur/segments/{segment}/status', [TransportSegmentController::class, 'updateStatus'])->name('segments.updateStatus');

});

//Gestion des annonces pour les commerçants
Route::middleware('auth')->group(function () {
    Route::get('commercant/annonces', [TraderAnnonceController::class, 'index'])->name('commercant.annonces.index');
    Route::get('commercant/annonces/create', [TraderAnnonceController::class, 'create'])->name('commercant.annonces.create');
    Route::post('commercant/annonces', [TraderAnnonceController::class, 'store'])->name('commercant.annonces.store');
    Route::get('commercant/annonces/{annonce}', [TraderAnnonceController::class, 'show'])->name('commercant.annonces.show');
    Route::get('commercant/annonces/{annonce}/edit', [TraderAnnonceController::class, 'edit'])->name('commercant.annonces.edit');
    Route::put('commercant/annonces/{annonce}', [TraderAnnonceController::class, 'update'])->name('commercant.annonces.update');
    Route::delete('commercant/annonces/{annonce}', [TraderAnnonceController::class, 'destroy'])->name('commercant.annonces.destroy');
});

//Actions dans le profil de l'utilisateur
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/livreur/documents', [ProfileController::class, 'documents'])->name('livreur.documents');
    Route::post('/livreur/documents', [ProfileController::class, 'uploadDocuments'])->name('livreur.documents.upload');
});
Route::get('/api/ors/route', [\App\Http\Controllers\MapController::class, 'routeBetweenCities']);


Route::patch('/admin/users/{id}/validate-documents', [AdminController::class, 'validateDocuments'])->name('admin.validateDocuments');

require __DIR__.'/auth.php';
