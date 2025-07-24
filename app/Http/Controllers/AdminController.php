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

    public function annoncesUpdate(Request $request, $id)
    {
        $annonce = \App\Models\Annonce::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'preferred_date' => 'nullable|date',
            'status' => 'required|in:publiée,prise en charge,complétée,archivée',
            'from_city' => 'nullable|string|max:255',
            'to_city' => 'nullable|string|max:255',
            'from_latitude' => 'nullable|numeric',
            'from_longitude' => 'nullable|numeric',
            'to_latitude' => 'nullable|numeric',
            'to_longitude' => 'nullable|numeric',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('uploads', 'public');
            $validated['photo'] = $path;
        }

        $validated['from_lat'] = $validated['from_latitude'] ?? null;
        $validated['from_lng'] = $validated['from_longitude'] ?? null;
        $validated['to_lat'] = $validated['to_latitude'] ?? null;
        $validated['to_lng'] = $validated['to_longitude'] ?? null;

        unset($validated['from_latitude'], $validated['from_longitude'], $validated['to_latitude'], $validated['to_longitude']);

        $annonce->update($validated);

        return redirect()->route('admin.annonces.show', $annonce->id)
            ->with('success', 'Annonce mise à jour avec succès.');
    }



    public function annoncesArchive($id)
    {
        $annonce = \App\Models\Annonce::findOrFail($id);
        $annonce->status = 'archivée';
        $annonce->save();

        return back()->with('success', 'Annonce archivée.');
    }

    public function annoncesRestore($id)
    {
        $annonce = \App\Models\Annonce::findOrFail($id);
        $annonce->status = 'publiée';
        $annonce->save();

        return redirect()->route('admin.annonces.show', $annonce->id)
            ->with('success', 'Annonce restaurée.');
    }

    public function annoncesDelete($id)
    {
        $annonce = \App\Models\Annonce::findOrFail($id);
        $annonce->delete();

        return redirect()->route('admin.annonces.index')
            ->with('success', 'Annonce supprimée.');
    }

    public function segmentsShow($id)
    {
        $segment = \App\Models\TransportSegment::with(['delivery', 'annonce.user'])
            ->findOrFail($id);

        return view('admin.segments.show', compact('segment'));
    }

    public function segmentsEdit($id)
    {
        $segment = \App\Models\TransportSegment::findOrFail($id);
        return view('admin.segments.edit', compact('segment'));
    }

    public function segmentsUpdate(Request $request, $id)
    {
        $segment = \App\Models\TransportSegment::findOrFail($id);

        $validated = $request->validate([
            'from_city' => 'required|string|max:255',
            'to_city' => 'required|string|max:255',
            'from_lat' => 'nullable|numeric',
            'from_lng' => 'nullable|numeric',
            'to_lat' => 'nullable|numeric',
            'to_lng' => 'nullable|numeric',
            'status' => 'required|in:en attente,accepté,refusé',
        ]);

        $segment->update($validated);

        return redirect()->route('admin.segments.show', $segment->id)
            ->with('success', 'Segment mis à jour avec succès.');
    }

    public function segmentsDestroy($id)
    {
        $segment = \App\Models\TransportSegment::findOrFail($id);
        $segment->delete();

        return redirect()->route('admin.annonces.show', $segment->annonce_id)
            ->with('success', 'Segment supprimé.');
    }

    public function indexDocuments()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès interdit');
        }
        return view('admin.documents.index');
    }




    public function documents()
{
    // On récupère uniquement les utilisateurs commerçants ayant un KBIS
    $users = User::where('role', 'commercant')
                 ->whereNotNull('kbis')
                 ->get();

    return view('admin.documents.documents', compact('users'));
}

    public function toggleKbisValidation($id)
{
    $user = User::findOrFail($id);

    // Inverse la valeur actuelle
    $user->kbis_valide = !$user->kbis_valide;
    $user->save();

    $message = $user->kbis_valide ? 'KBIS validé avec succès.' : 'Validation du KBIS annulée.';

    return redirect()->route('admin.documents.documents_commercant')->with('success', $message);
}

    public function indexLivreurs()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Accès interdit');
        }

        // Récupérer les livreureuhs qui ont envoyé leurs documents mais qui ne sont pas encore validés

        $livreurs = \App\Models\User::where('role', 'livreur')
            ->whereNotNull('identity_document')
            ->whereNotNull('driver_license')
            ->where('documents_verified', false)
            ->get();

        return view('admin.documents.documents_livreurs', compact('livreurs'));
    }


 public function validateDocuments($id)
{
    if (Auth::user()->role !== 'admin') {
        abort(403, 'Accès interdit');
    }

    $user = User::findOrFail($id);
    $user->documents_verified = true;
    $user->save();

    return redirect()->back()->with('success', 'Documents validés.');
}

}
