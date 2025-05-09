<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

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

    Route::get('/admin/dashboard/', function () {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès interdit');
        }
        return view('dashboards.admin');
    })->name('admin.dashboard');

});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



require __DIR__.'/auth.php';
