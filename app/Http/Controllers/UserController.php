<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\Role;
use App\Models\Stock;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
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

        return view('gestuser.index', compact('users', 'roles', 'services'));
    }

    public function create()
    {
        $roles = Role::all();
        $services = Service::all();
        $stocks = Stock::all(); // ✅ Utiliser les stocks de la base
        return view('gestuser.create', compact('roles', 'services', 'stocks'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'service_id' => 'required|exists:services,id',
            'role_id' => 'required|exists:roles,id',
            'stock_id' => 'nullable|exists:stocks,id', // ✅ Ajout
        ]);

        $plainPassword = $validatedData['password'];
        $validatedData['password'] = bcrypt($validatedData['password']);

        $validatedData['photo'] = isset($validatedData['photo']) 
            ? $validatedData['photo']->store('photos', 'public') 
            : null;

        $user = User::create($validatedData);

        $pdf = PDF::loadView('pdf.user_credentials', [
            'user' => $user,
            'password' => $plainPassword
        ]);

        return redirect()->route('gestuser.index')->with('success', 'Utilisateur créé avec succès.')->with('download.in.the.next.request', [
            'url' => base64_encode($pdf->output()),
            'name' => 'identifiants_'.$user->nom.'_'.$user->prenom.'.pdf'
        ]);
    }

    public function show(string $id)
    {
        $user = User::findOrFail($id);
        $stock = $user->stock;
        return view('gestuser.show', compact('user', 'stock'));
    }

    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $services = Service::all();
        $stocks = Stock::all(); // ✅ Ajout
        return view('gestuser.edit', compact('user', 'roles', 'services', 'stocks'));
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'prenom' => 'sometimes|required|string|max:255',
            'telephone' => 'sometimes|required|string|max:20',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'service_id' => 'sometimes|required|exists:services,id',
            'role_id' => 'sometimes|required|exists:roles,id',
            'stock_id' => 'nullable|exists:stocks,id', // ✅ Ajout
        ]);

        if (!empty($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        if (isset($validatedData['photo'])) {
            $validatedData['photo'] = $validatedData['photo']->store('photos', 'public');
        } else {
            unset($validatedData['photo']);
        }

        $user->update($validatedData);

        return redirect()->route('gestuser.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('gestuser.index')->with('success', 'Utilisateur supprimé avec succès.');
    }
}
