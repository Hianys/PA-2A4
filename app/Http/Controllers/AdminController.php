<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{

    //Dashboard Admin
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès interdit');
        }

        return view('dashboards.admin');
    }

    //Partie Users

    public function users(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès interdit');
        }

        $role = $request->query('role');

        $query = User::query();

        if ($role) {
            $query->where('role', $role);
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        return view('admin.users.index', compact('users', 'role'));
    }

    public function showUser($id)
    {
        $user = User::findOrFail($id);

        $annonces = [];
        $segments = [];

        if ($user->role === 'client' || $user->role === 'commercant') {
            $annonces = $user->annonces()->latest()->get();
        }

        if ($user->role === 'livreur') {
            $segments = $user->segmentsTaken()->latest()->get();
        }

        return view('admin.users.show', compact('user', 'annonces', 'segments'));
    }

    public function updateUser(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $user = \App\Models\User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,client,livreur,prestataire,commercant',
        ]);

        $user->update($request->only(['name', 'email', 'role']));

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', 'Utilisateur mis à jour.');
    }


    public function editUser($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $user = \App\Models\User::findOrFail($id);

        return view('admin.users.edit', compact('user'));
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

    //Partie Annonces

    public function annoncesIndex()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $annonces = \App\Models\Annonce::with('user')->latest()->get();

        return view('admin.annonces.index', compact('annonces'));
    }

    public function annoncesShow($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $annonce = \App\Models\Annonce::with('user')->findOrFail($id);

        return view('admin.annonces.show', compact('annonce'));
    }

    public function annoncesEdit($id)
    {
        $annonce = \App\Models\Annonce::findOrFail($id);

        return view('admin.annonces.edit', compact('annonce'));
    }

    public function annoncesArchive($id)
    {
        $annonce = \App\Models\Annonce::findOrFail($id);
        $annonce->status = 'archivée';
        $annonce->save();

        return back()->with('success', 'Annonce archivée.');
    }

    public function annoncesDelete($id)
    {
        $annonce = \App\Models\Annonce::findOrFail($id);
        $annonce->delete();

        return redirect()->route('admin.annonces.index')
            ->with('success', 'Annonce supprimée.');
    }

}
