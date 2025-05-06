<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\Role;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        if ($request->filled('service')) {
            $query->where('service_id', $request->service);
        }

        $users = $query->get();
        $roles = Role::all();
        $services = Service::all();

        return view('employe.index', compact('users', 'roles', 'services'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        $services = Service::all();
        return view('employe.create', compact('roles', 'services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'photo' => 'nullable|string',
            'service_id' => 'required|exists:services,id',
            'role_id' => 'required|exists:roles,id',
        ]);
    
        // Sauvegardez le mot de passe en clair pour le PDF
        $plainPassword = $validatedData['password'];
        
        // Cryptez le mot de passe pour la base de données
        $validatedData['password'] = bcrypt($validatedData['password']);
    
        // Créez l'utilisateur
        $user = User::create($validatedData);
    
        // Générez le PDF
        $pdf = PDF::loadView('pdf.user_credentials', [
            'user' => $user,
            'password' => $plainPassword
        ]);
    
        // Option 1 : Téléchargement immédiat du PDF
        return $pdf->download('identifiants_' . $user->nom . '_' . $user->prenom . '.pdf');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return view('employe.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('employe.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'prenom' => 'sometimes|required|string|max:255',
            'telephone' => 'sometimes|required|string|max:20',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'photo' => 'nullable|string',
            'service_id' => 'sometimes|required|exists:services,id',
            'role_id' => 'sometimes|required|exists:roles,id',
        ]);

        if (!empty($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        $user->update($validatedData);

        return redirect()->route('employe.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('employe.index')->with('success', 'Utilisateur supprimé avec succès.');
    }
}
