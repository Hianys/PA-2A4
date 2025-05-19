<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès interdit');
        }

        $users = User::orderBy('created_at', 'desc')->get();
        return view('dashboards.admin', compact('users'));
    }

    public function promote($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès interdit');
        }
        $user = User::findOrFail($id);
        $user->role = 'admin';
        $user->save();

        return redirect()->route('admin.dashboard')->with('success', 'Utilisateur promu en admin.');
    }

    // Rétrograder un utilisateur en client
    public function demote($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès interdit');
        }
        $user = User::findOrFail($id);
        $user->role = 'client';
        $user->save();

        return redirect()->route('admin.dashboard')->with('success', 'Utilisateur rétrogradé.');
    }

    // Supprimer un utilisateur
    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès interdit');
        }
        $user = User::findOrFail($id);

        // Empêche de se supprimer soi-même
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.dashboard')->with('error', 'Vous ne pouvez pas vous supprimer vous-même.');
        }

        $user->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Utilisateur supprimé.');
    }

}
