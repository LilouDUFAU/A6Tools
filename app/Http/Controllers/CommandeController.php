<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Client;
use App\Models\User;
use App\Models\Produit;
use App\Models\Fournisseur;
use Illuminate\Http\Request;

class CommandeController extends Controller
{
    /**
     * Affiche la liste des commandes.
     */
    public function index()
    {
        $commandes = Commande::with(['client', 'employe'])->get();
        return view('gestock.index', compact('commandes'));
    }

    /**
     * Affiche le formulaire de création d'une commande.
     */
    public function create()
    {
        $clients = Client::all();
        $types = Client::TYPES;
        $etats = Commande::ETATS;
        $urgences = Commande::URGENCES;

        return view('gestock.create', compact('clients', 'types' , 'etats', 'urgences'));
    }

    /**
     * Enregistre une nouvelle commande.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'intitule' => 'required|string|max:255',
            'prix_total' => 'required|numeric',
            'etat' => 'required|string|max:255',
            'remarque' => 'nullable|string',
            'urgence' => 'nullable',
            'date_livraison_fournisseur' => 'nullable|date',
            'date_installation_prevue' => 'nullable|date',
            'client_id' => 'nullable|exists:clients,id',
        ]);

        $validated['employe_id'] = auth()->user()->id;

        // Nouveau client ?
        if ($request->has('new_client.nom') && $request->input('new_client.nom')) {
            $validatedClient = $request->validate([
                'new_client.nom' => 'required|string|max:255',
                'new_client.email' => 'required|email|unique:clients,email',
                'new_client.telephone' => 'required|string|max:15',
                'new_client.adresse_postale' => 'required|string|max:255',
                'new_client.type' => 'required|string|max:255',
            ]);
        
            $client = Client::create([
                'nom' => $request->input('new_client.nom'),
                'email' => $request->input('new_client.email'),
                'telephone' => $request->input('new_client.telephone'),
                'adresse_postale' => $request->input('new_client.adresse_postale'),
                'type' => $request->input('new_client.type'),
            ]);
            $validated['client_id'] = $client->id;
        }

        // Créer la commande
        $commande = Commande::create($validated);

        // Ajouter les produits et leurs fournisseurs
        foreach ($request->input('produits', []) as $produitData) {
            // Ajouter ou récupérer le fournisseur
            $fournisseur = null;
            if (!empty($produitData['fournisseur']['nom'])) {
                $fournisseur = Fournisseur::firstOrCreate(
                    ['nom' => $produitData['fournisseur']['nom']],
                    [
                        'email' => $produitData['fournisseur']['email'] ?? null,
                        'telephone' => $produitData['fournisseur']['telephone'] ?? null,
                        'adresse_postale' => $produitData['fournisseur']['adresse_postale'] ?? null,
                    ]
                );
            }

            // Ajouter ou récupérer le produit
            $produit = Produit::firstOrCreate(
                ['reference' => $produitData['reference']],
                [
                    'nom' => $produitData['nom'],
                    'description' => $produitData['description'] ?? '',
                    'caracteristiques_techniques' => $produitData['caracteristiques_techniques'] ?? '',
                    'quantite_stock' => $produitData['quantite_stock'] ?? 0,
                    'quantite_client' => $produitData['quantite'] ?? 0,
                    'prix' => $produitData['prix'] ?? 0,
                    'image' => $produitData['image'] ?? null,
                ]
            );

            // Lier le produit au fournisseur
            if ($fournisseur) {
                $produit->fournisseurs()->syncWithoutDetaching([$fournisseur->id]);
            }


            $quantite_stock = $produit->quantite_stock;
            $quantite_client = $produit->quantite_client; // Quantité demandée par le client

            // Attacher le produit à la commande via la table pivot `commande_produit`
            $commande->produits()->attach($produit->id, [
                'quantite' => $quantite_stock + $quantite_client // Quantité totale 
            ]);
        }

        // Retourner à la liste des commandes avec un message de succès
        return redirect()->route('commande.index')->with('success', 'Commande créée avec succès.');
    }


    /**
     * Affiche les détails d'une commande.
     */
    public function show(string $id)
    {
        $commande = Commande::with(['client', 'employe', 'produits'])->findOrFail($id);
        return view('gestock.show', compact('commande'));
    }

    /**
     * Affiche le formulaire d'édition d'une commande.
     */
    public function edit(string $id)
    {
        $commande = Commande::findOrFail($id);
        $clients = Client::all();

        return view('gestock.edit', compact('commande', 'clients'));
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