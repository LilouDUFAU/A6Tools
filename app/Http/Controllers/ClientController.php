<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Afficher tous les clients.
     */
    public function index()
    {
        $clients = Client::all();
        return view('clients.index', compact('clients'));
    }

    /**
     * Afficher le formulaire de création d'un client.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Enregistrer un nouveau client.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code_client' => 'required|string|max:255|unique:clients,code_client',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'Client créé avec succès.');
    }

    /**
     * Afficher un client spécifique.
     */
    public function show(string $id)
    {
        $client = Client::findOrFail($id);
        return view('clients.show', compact('client'));
    }

    /**
     * Afficher le formulaire d'édition d'un client.
     */
    public function edit(string $id)
    {
        $client = Client::findOrFail($id);
        return view('clients.edit', compact('client'));
    }

    /**
     * Mettre à jour les informations d'un client.
     */
    public function update(Request $request, string $id)
    {
        $client = Client::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code_client' => 'required|string|max:255|unique:clients,code_client,' . $client->id,
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'Client mis à jour avec succès.');
    }

    /**
     * Supprimer un client.
     */
    public function destroy(string $id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client supprimé avec succès.');
    }
}