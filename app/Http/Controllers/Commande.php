<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;

class CommandeController extends Controller
{
    /**
     * Affiche la liste des commandes.
     */
    public function index()
    {
        $commandes = Commande::with(['client', 'employe'])->get();
        return view('gestock.commandes.index', compact('commandes'));
    }

    /**
     * Affiche le formulaire de création d'une commande.
     */
    public function create()
    {
        $clients = Client::all();
        // L'employé est celui qui est connecté, on ne le passe pas dans le formulaire
        return view('gestock.commandes.create', compact('clients'));
    }

    /**
     * Enregistre une nouvelle commande.
     */
    public function store(Request $request)
    {
        // Ajout de l'employé connecté
        $validated = $request->validate([
            'intitule' => 'required|string|max:255',
            'prix_total' => 'required|numeric',
            'etat' => 'required|string|max:255',
            'remarque' => 'nullable|string',
            'urgence' => 'nullable|boolean',
            'date_livraison_fournisseur' => 'nullable|date',
            'date_installation_prevue' => 'nullable|date',
            'client_id' => 'nullable|exists:clients,id',
        ]);

        // Ajouter l'ID de l'employé connecté (auth()->user()->id)
        $validated['employe_id'] = auth()->user()->id;

        Commande::create($validated);

        return redirect()->route('commande.index')->with('success', 'Commande créée avec succès.');
    }

    /**
     * Affiche les détails d'une commande.
     */
    public function show(string $id)
    {
        $commande = Commande::with(['client', 'employe'])->findOrFail($id);
        return view('gestock.commandes.show', compact('commande'));
    }

    /**
     * Affiche le formulaire d'édition d'une commande.
     */
    public function edit(string $id)
    {
        $commande = Commande::findOrFail($id);
        $clients = Client::all();

        return view('gestock.commandes.edit', compact('commande', 'clients'));
    }

    /**
     * Met à jour une commande existante.
     */
    public function update(Request $request, string $id)
    {
        // Ajouter l'ID de l'employé connecté si il n'est pas dans la demande
        $validated = $request->validate([
            'intitule' => 'required|string|max:255',
            'prix_total' => 'required|numeric',
            'etat' => 'required|string|max:255',
            'remarque' => 'nullable|string',
            'urgence' => 'nullable|boolean',
            'date_livraison_fournisseur' => 'nullable|date',
            'date_installation_prevue' => 'nullable|date',
            'client_id' => 'nullable|exists:clients,id',
        ]);

        // Ajouter l'ID de l'employé connecté (auth()->user()->id)
        $validated['employe_id'] = auth()->user()->id;

        $commande = Commande::findOrFail($id);
        $commande->update($validated);

        return redirect()->route('commande.index')->with('success', 'Commande mise à jour avec succès.');
    }

    /**
     * Supprime une commande.
     */
    public function destroy(string $id)
    {
        $commande = Commande::findOrFail($id);
        $commande->delete();

        return redirect()->route('commande.index')->with('success', 'Commande supprimée avec succès.');
    }
}