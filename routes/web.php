<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Client\AnnonceController as ClientAnnonceController;
use App\Http\Controllers\Delivery\AnnonceController as DeliveryAnnonceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Trader\AnnonceController as TraderAnnonceController;
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
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users.index');
    Route::get('/users/{id}', [AdminController::class, 'showUser'])->name('admin.users.show');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::patch('/admin/users/{id}/promote', [AdminController::class, 'promote'])->name('admin.promote');
    Route::patch('/admin/users/{id}/demote', [AdminController::class, 'demote'])->name('admin.demote');
    Route::delete('/admin/users/{id}', [AdminController::class, 'destroy'])->name('admin.delete');


    Route::get('/admin/annonces', [AdminController::class, 'annoncesIndex'])->name('admin.annonces.index');
    Route::get('/admin/annonces/{annonce}', [AdminController::class, 'annoncesShow'])->name('admin.annonces.show');
    Route::get('/admin/annonces/{annonce}/edit', [AdminController::class, 'annoncesEdit'])->name('admin.annonces.edit');
    Route::get('/admin/annonces/{annonce}/update', [AdminController::class, 'annoncesUpdate'])->name('admin.annonces.update');
    Route::patch('/admin/annonces/{annonce}/archive', [AdminController::class, 'annoncesArchive'])->name('admin.annonces.archive');
    Route::delete('/admin/annonces/{annonce}', [AdminController::class, 'annoncesDelete'])->name('admin.annonces.delete');

    Route::get('/admin/segments/{segment}', [AdminController::class, 'segmentsShow'])->name('admin.segments.show');
    Route::get('/admin/segments/{segment}/edit', [AdminController::class, 'segmentsEdit'])->name('admin.segments.edit');
    Route::put('/admin/segments/{segment}', [AdminController::class, 'segmentsUpdate'])->name('admin.segments.update');
    Route::delete('/admin/segments/{segment}', [AdminController::class, 'segmentsDestroy'])->name('admin.segments.destroy');




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
    Route::post('/segments/{segment}/accept', [TransportSegmentController::class, 'accept'])->name('segments.accept');
    Route::post('/segments/{segment}/refuse', [TransportSegmentController::class, 'refuse'])->name('segments.refuse');

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

Route::get('/api/ors/route', [\App\Http\Controllers\MapController::class, 'routeBetweenCities']);


require __DIR__.'/auth.php';
