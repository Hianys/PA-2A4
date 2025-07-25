<?php

//importe la classe en allant chercher ses sous-dossier
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\Client\AnnonceController as ClientAnnonceController;
use App\Http\Controllers\Trader\AnnonceController as TraderAnnonceController;
use App\Http\Controllers\Delivery\AnnonceController as DeliveryAnnonceController;
use App\Http\Controllers\Provider\AnnonceController as ProviderAnnonceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransportSegmentController;

//acces aux fonctionnalités LARAVEL  
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;



Route::get('/changeLocale/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'es', 'fr', 'ar'])) {
        //récupère la variable $locale et on la met dans localeuh
        session()->put('locale', $locale);
     }
     //retoureuh page précedente mais mtn avec la variable localeuh
    return redirect()->back();
});

//Route de la page d'accueilleuh
Route::get('/', function () {
    return view('home');
})->name('home');


//Routes des différents dashboardeuhs
Route::middleware(['auth'])->group(function () {
    Route::get('/client/dashboard', function () {
        return view('dashboards.client');
    })->name('client.dashboard');

    Route::get('/livreur/dashboard', function () {
        return view('dashboards.delivery');
    })->name('delivery.dashboard');

    Route::get('/commercant/dashboard', [TraderAnnonceController::class, 'dashboard'])
        ->name('trader.dashboard');

    Route::get('/prestataire/dashboard', [ProviderAnnonceController::class, 'dashboard'])
        ->name('provider.dashboard');

    Route::get('/admin/dashboard/', [AdminController::class, 'index'])
        ->name('admin.dashboard');

});

//Actions utilisateurs admineuhs
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users.index');
    Route::get('/users/{id}', [AdminController::class, 'showUser'])->name('admin.users.show');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::patch('/admin/users/{id}/promote', [AdminController::class, 'promote'])->name('admin.promote');
    Route::patch('/admin/users/{id}/demote', [AdminController::class, 'demote'])->name('admin.demote');
    Route::delete('/admin/users/{id}', [AdminController::class, 'destroy'])->name('admin.delete');
    Route::get('/livreur/documents', [ProfileController::class, 'documents'])->name('livreur.documents');
    Route::post('/livreur/documents', [ProfileController::class, 'uploadDocuments'])->name('livreur.documents.upload');
    Route::get('/admin/documents', [AdminController::class, 'documents'])->name('admin.documents');
    Route::patch('/admin/kbis/{id}/toggle', [AdminController::class, 'toggleKbisValidation'])->name('admin.kbis.toggle');

    Route::get('/admin/annonces', [AdminController::class, 'annoncesIndex'])->name('admin.annonces.index');
    Route::get('/admin/annonces/{annonce}', [AdminController::class, 'annoncesShow'])->name('admin.annonces.show');
    Route::get('/admin/annonces/{annonce}/edit', [AdminController::class, 'annoncesEdit'])->name('admin.annonces.edit');
    Route::get('/admin/annonces/{annonce}/update', [AdminController::class, 'annoncesUpdate'])->name('admin.annonces.update');
    Route::patch('/admin/annonces/{annonce}/archive', [AdminController::class, 'annoncesArchive'])->name('admin.annonces.archive');
    Route::patch('/admin/annonces/{annonce}/restore', [AdminController::class, 'annoncesRestore'])->name('admin.annonces.restore');
    Route::delete('/admin/annonces/{annonce}', [AdminController::class, 'annoncesDelete'])->name('admin.annonces.delete');

    Route::get('/admin/segments/{segment}', [AdminController::class, 'segmentsShow'])->name('admin.segments.show');
    Route::get('/admin/segments/{segment}/edit', [AdminController::class, 'segmentsEdit'])->name('admin.segments.edit');
    Route::put('/admin/segments/{segment}', [AdminController::class, 'segmentsUpdate'])->name('admin.segments.update');
    Route::delete('/admin/segments/{segment}', [AdminController::class, 'segmentsDestroy'])->name('admin.segments.destroy');




});

//Gestion des annonces pour les clients
//on appelle le middleware et verifie si l'utilisateur est bien authentifié puis avec la grosseuh fonction on regrpe les routeuh
Route::middleware(['auth'])->group(function () {
    Route::get('/client/annonces', [ClientAnnonceController::class, 'index'])->name('client.annonces.index');
    //appelle méthode get, indique l'URL , précise la fonction a utilisé du controller, et enfin on donne a un nom a la rouetuh 
    Route::get('/client/annonces/create', [ClientAnnonceController::class, 'create'])->name('client.annonces.create');
    Route::get('/client/annonces/{annonce}', [ClientAnnonceController::class, 'show'])->name('client.annonces.show');
    Route::get('/client/annonces/{annonce}/edit', [ClientAnnonceController::class, 'edit'])->name('client.annonces.edit');
    Route::post('/client/annonces', [ClientAnnonceController::class, 'store'])->name('client.annonces.store');
    Route::put('/client/annonces/{annonce}', [ClientAnnonceController::class, 'update'])->name('client.annonces.update');
    Route::delete('/client/annonces/{annonce}', [ClientAnnonceController::class, 'destroy'])->name('client.annonces.destroy');
    Route::post('/segments/{segment}/accept', [TransportSegmentController::class, 'accept'])->name('segments.accept');
    Route::post('/segments/{segment}/refuse', [TransportSegmentController::class, 'refuse'])->name('segments.refuse');
    Route::put('/client/annonces/{annonce}/validate', [ClientAnnonceController::class, 'confirmDelivery'])->name('client.annonces.validate');
    Route::post('/client/annonces/{annonce}/payer', [ClientAnnonceController::class, 'payAnnonce'])->name('client.annonces.payer');
    Route::post('/delivery/{delivery}/pay', [ClientAnnonceController::class, 'payDelivery'])->name('delivery.pay');
    Route::post('/client/annonces/{annonce}/valider-segments', [ClientAnnonceController::class, 'validateTransportAnnonce'])->name('client.annonces.validate.segments');
    Route::post('/client/annonces/{annonce}/confirmer-transport', [ClientAnnonceController::class, 'confirmTransportDelivery'])->name('client.annonces.confirmTransport');
    Route::post('/client/annonces/{annonce}/mark-awaiting-payment', [ClientAnnonceController::class, 'markAsAwaitingPayment'])->name('client.annonces.markAwaitingPayment');

});

//Prise en charge des annonces de type transport pour les Livreureuhs
Route::middleware('auth')->group(function () {
    Route::get('/livreur/annonces', [DeliveryAnnonceController::class, 'index'])->name('delivery.annonces.index');
    Route::get('/livreur/annonces/{annonce}', [DeliveryAnnonceController::class, 'show'])->name('delivery.annonces.show');
    Route::post('/livreur/annonces/{annonce}/segment', [TransportSegmentController::class, 'store'])->name('segments.store');
    Route::get('/livreur/mes-livraisons', [TransportSegmentController::class, 'mesLivraisons'])->name('delivery.segments.index');
    Route::patch('livreur/segments/{segment}/status', [TransportSegmentController::class, 'updateStatus'])->name('segments.updateStatus');
    Route::post('/livreur/annonces/{annonce}/confirmer', [DeliveryAnnonceController::class, 'confirmerAnnonce'])->name('delivery.annonces.confirmer');
    Route::post('/livreur/annonces/{annonce}/marquer-en-attente', [DeliveryAnnonceController::class, 'marquerEnAttenteDePaiement'])->name('delivery.annonces.markPending');
    Route::get('/delivery/mes-annonces', [DeliveryAnnonceController::class, 'mesAnnonces'])->name('delivery.annonces.mes');



});

//Gestion des annonces pour les commeuhrçants
Route::middleware('auth')->group(function () {
    Route::get('commercant/annonces', [TraderAnnonceController::class, 'index'])->name('commercant.annonces.index');
    Route::get('commercant/annonces/create', [TraderAnnonceController::class, 'create'])->name('commercant.annonces.create');
    Route::post('commercant/annonces', [TraderAnnonceController::class, 'store'])->name('commercant.annonces.store');
    Route::get('commercant/annonces/{annonce}', [TraderAnnonceController::class, 'show'])->name('commercant.annonces.show');
    Route::get('commercant/annonces/{annonce}/edit', [TraderAnnonceController::class, 'edit'])->name('commercant.annonces.edit');
    Route::put('commercant/annonces/{annonce}', [TraderAnnonceController::class, 'update'])->name('commercant.annonces.update');
    Route::delete('commercant/annonces/{annonce}', [TraderAnnonceController::class, 'destroy'])->name('commercant.annonces.destroy');
    Route::post('/commercant/annonces/{annonce}/complete', [TraderAnnonceController::class, 'markCompleted'])->name('commercant.annonces.complete');
    Route::get('/commercant/dashboard', [TraderAnnonceController::class, 'dashboard'])->name('trader.dashboard');
    Route::post('/commercant/annonces/{annonce}/payer', [TraderAnnonceController::class, 'payAnnonce'])->name('commercant.annonces.payer');
    Route::post('/commercant/annonces/{annonce}/confirm-payments', [\App\Http\Controllers\Trader\AnnonceController::class, 'confirmPayments'])->name('commercant.annonces.confirmPayments');
    
    //ici on appelle via le chemin (pas spécialement utile mais ca se fait)
    Route::get('/annonces/{annonce}/facture', [\App\Http\Controllers\Trader\AnnonceController::class, 'facturePdf'])->middleware('auth')->name('annonce.facture.pdf');


    // Profil commerçant
    Route::get('/commercant/profil', [TraderAnnonceController::class, 'editProfile'])->name('commercant.profile.edit');
    Route::post('/commercant/profil', [TraderAnnonceController::class, 'updateProfile'])->name('commercant.profile.update');

    Route::post('/commercant/consentement', [TraderAnnonceController::class, 'generateConsentPdf'])->name('commercant.consentement.submit');
    Route::get('/commercant/consentement', [TraderAnnonceController::class, 'showConsentForm'])->name('commercant.consentement.form');
    Route::post('/commercant/consentement/valider', [TraderAnnonceController::class, 'validerConsentement'])->name('commercant.consentement.valider');
    Route::post('/commercant/consentement/pdf', [TraderAnnonceController::class, 'telechargerPdf'])->name('commercant.consentement.pdf');
});

//Actions dans le profil de l'utilisateureuh
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Gestion des annonces pour les prestataireuhs
Route::middleware(['auth'])->group(function () {
    Route::get('/prestataire/annonces', [ProviderAnnonceController::class, 'index'])->name('provider.annonces.index');
    Route::get('/prestataire/annonces/{annonce}', [ProviderAnnonceController::class, 'show'])->name('provider.annonces.show');
    Route::post('/prestataire/annonces/{annonce}/accept', [ProviderAnnonceController::class, 'accept'])->name('provider.annonces.accept');
    Route::patch('/prestataire/annonces/{annonce}/complete', [ProviderAnnonceController::class, 'markCompleted'])->name('provider.annonces.complete');
    Route::get('/prestataire/missions', [ProviderAnnonceController::class, 'missions'])->name('provider.annonces.missions');
    Route::post('/prestataire/annonces/{annonce}/confirmer', [ProviderAnnonceController::class, 'confirmerAnnonce'])->name('provider.annonces.confirmer');

});

Route::get('/upload', function () {
    return view('upload');
})->name('file.upload.form');

//pour la récupération de fichier uploadé 
Route::post('/upload', function (Request $request) {
    if ($request->hasFile('fichier')) {
        $file = $request->file('fichier');

        if (!$file->isValid()) {
            return back()->with('error', 'Le fichier est invalide.');
        }
        //dans la var on stock $file qui est stocké dans public/uploads
        $path = $file->store('uploads', 'public'); 

        return redirect()->route('file.upload.form')->with('success', 'Fichier uploadé dans : ' . $path);
    }

    return back()->with('error', 'Aucun fichier sélectionné.');
})->name('file.upload');

Route::get('/test-auth', function () {
    return Auth::check() ? 'Connecté en tant que : ' . Auth::user()->email : 'Non connecté';
});
Route::get('/api/ors/route', [\App\Http\Controllers\MapController::class, 'routeBetweenCities']);


Route::patch('/admin/users/{id}/validate-documents', [AdminController::class, 'validateDocuments'])->name('admin.validateDocuments');

// Route pour afficher les documents depuis le stockage
Route::get('/document/{filename}', function ($filename) {
    $path = storage_path('app/public/documents/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }
    //renvoie dans file le conteuhnu en binaireuh
    $file = file_get_contents($path);
    //renvoie le typeuh du fichier (png ect)
    $type = mime_content_type($path);

    return response($file, 200)->header('Content-Type', $type);
})->middleware('auth')->name('documents.show');

Route::middleware(['auth'])->group(function () {
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/checkout', [WalletController::class, 'checkout'])->name('wallet.checkout');
    Route::get('/wallet/success', [WalletController::class, 'success'])->name('wallet.success');
    Route::post('/wallet/withdraw', [WalletController::class, 'withdraw'])->name('wallet.withdraw');


    Route::post('/delivery/{delivery}/pay', [ClientAnnonceController::class, 'payDelivery'])->name('delivery.pay');
    Route::post('/delivery/{delivery}/confirm', [ClientAnnonceController::class, 'confirmDelivery'])->name('delivery.confirm.client');
    Route::post('/delivery/{delivery}/confirm-delivery', [DeliveryAnnonceController::class, 'confirmDelivery'])->name('delivery.confirm.delivery');
});

require __DIR__.'/auth.php';
