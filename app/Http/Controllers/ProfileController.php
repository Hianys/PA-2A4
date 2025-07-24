<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

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
        return view('delivery.documents');
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

        $user->identity_document = 'placeholder_identity.pdf'; // ou null
        $user->driver_license = 'placeholder_license.pdf';
        $user->documents_verified = false;
        $user->save();

        return redirect()->route('delivery.dashboard')->with('success', 'Documents soumis. En attente de validation.');
    }

}
