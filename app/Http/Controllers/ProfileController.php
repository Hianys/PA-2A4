<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**e
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Show the document upload form for livreur.
     */
    public function documents(): View
    {
        return view('profile.documents');
    }

    /**
     * Handle document upload for livreur.
     */
public function uploadDocuments(Request $request): RedirectResponse
{
    $request->validate([
        'identity_document' => 'required|file|mimes:pdf,jpg,png|max:2048',
        'driver_license' => 'required|file|mimes:pdf,jpg,png|max:2048',
    ]);

    $user = Auth::user();

    // Stockage des fichiers dans le disque "public"
    $path1 = $request->file('identity_document')->store('documents', 'public');
    $path2 = $request->file('driver_license')->store('documents', 'public');

    // Affectation dans l'objet utilisateur
    $user->identity_document = $path1;
    $user->driver_license = $path2;
    $user->documents_verified = false;

    // Sauvegarde en base
    $user->save();

    // DEBUG : affiche les chemins enregistrés
    dd([
        'stored_identity_path' => $path1,
        'stored_license_path' => $path2,
        'user_saved' => $user->identity_document && $user->driver_license,
    ]);

    // return back()->with('success', 'Documents envoyés. En attente de validation.');
}

}
