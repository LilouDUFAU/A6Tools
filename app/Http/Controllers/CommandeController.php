<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Client;
use App\Models\User;
use App\Models\Produit;
use App\Models\Fournisseur;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $stocks = Stock::LIEUX;

        return view('gestock.create', compact('clients', 'types' , 'etats', 'urgences', 'stocks'));
    }

    /**
     * Enregistre une nouvelle commande.
     */
    public function store(Request $request)
    {
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

        // Récupère le lieu sélectionné dans le formulaire
        $lieu = $request->input('lieu');
        $stock = null;
        if (!empty($lieu)) {
            // Crée ou récupère un stock avec ce lieu
            $stock = Stock::firstOrCreate(['lieu' => $lieu]);
            
            // Associe le stock à la commande, si le stock existe
            if ($stock) {
                $commande->stocks()->syncWithoutDetaching([$stock->id]);
            }
        }

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
                    'prix' => $produitData['prix'] ?? 0,
                    'lien_produit_fournisseur' => $produitData['lien_produit_fournisseur'] ?? null,
                ]
            );

            $produit->fill([
                'nom' => $produitData['nom'],
                'description' => $produitData['description'] ?? '',
                'caracteristiques_techniques' => $produitData['caracteristiques_techniques'] ?? '',
                'prix' => $produitData['prix'] ?? 0,
                'lien_produit_fournisseur' => $produitData['lien_produit_fournisseur'] ?? null,
            ])->save();



            // Lier le produit au fournisseur
            if ($fournisseur) {
                $produit->fournisseurs()->syncWithoutDetaching([$fournisseur->id]);
            }

            // Attacher le produit à la commande via la table pivot `commande_produit`
            $commande->produits()->attach($produit->id, [
                'quantite' => $produitData['quantite_stock'] + $produitData['quantite_client'],
                'quantite_stock' => $produitData['quantite_stock'],
                'quantite_client' => $produitData['quantite_client'],
            ]);

            // Calculer la quantité totale (stock + client) et mettre à jour produit_stock
            $quantite_totale = $produitData['quantite_stock'] + $produitData['quantite_client'];

            if ($request->has('stock_id')) {
                $stockId = $request->input('stock_id');
                DB::table('produit_stock')->insert([
                    'produit_id' => $produit->id,
                    'stock_id' => $stockId,
                    'commande_id' => $commande->id,
                    'quantite' => $quantite_totale, // Quantité totale (stock + quantité commandée)
                    'created_at' => now(), // Horodatage de la création
                    'updated_at' => now(), // Horodatage de la mise à jour
                ]);
            }
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
        // Récupérer la commande avec ses produits et son client
        $commande = Commande::with(['produits', 'client'])->findOrFail($id);
        
        // Récupérer tous les clients et stocks disponibles
        $clients = Client::all();
        $stocks = Stock::all(); // Tous les stocks

        // Récupérer les autres données pour le formulaire
        $types = Client::TYPES;
        $etats = Commande::ETATS;
        $urgences = Commande::URGENCES;

        // Renvoyer la vue avec les données nécessaires
        return view('gestock.edit', compact('commande', 'clients', 'stocks', 'types', 'etats', 'urgences'));
    }

    /**
     * Met à jour une commande existante.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'intitule' => 'required|string|max:255',
            'prix_total' => 'required|numeric',
            'etat' => 'required|string|max:255',
            'remarque' => 'nullable|string',
            'urgence' => 'nullable|string|max:255',
            'date_livraison_fournisseur' => 'nullable|date',
            'date_installation_prevue' => 'nullable|date',
            'client_id' => 'nullable|exists:clients,id',
        ]);

        $validated['employe_id'] = auth()->user()->id;

        // Trouver la commande existante
        $commande = Commande::findOrFail($id);
        $commande->update($validated);

        // Récupérer et associer un lieu de stockage
        $lieu = $request->input('lieu');
        $stock = null;
        if (!empty($lieu)) {
            $stock = Stock::firstOrCreate(['lieu' => $lieu]);
            // Associe le stock à la commande
            $commande->stocks()->syncWithoutDetaching([$stock->id]);
        }

        // Mettre à jour les produits associés à la commande
        foreach ($request->input('produits', []) as $produitData) {
            // Mettre à jour ou ajouter un fournisseur
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

            // Mettre à jour ou créer un produit
            $produit = Produit::firstOrCreate(
                ['reference' => $produitData['reference']],
                [
                    'nom' => $produitData['nom'],
                    'description' => $produitData['description'] ?? '',
                    'caracteristiques_techniques' => $produitData['caracteristiques_techniques'] ?? '',
                    'prix' => $produitData['prix'] ?? 0,
                    'image' => $produitData['image'] ?? null,
                ]
            );

            // Lier le produit au fournisseur
            if ($fournisseur) {
                $produit->fournisseurs()->syncWithoutDetaching([$fournisseur->id]);
            }

            // Attacher le produit à la commande via la table pivot `commande_produit`
            $commande->produits()->attach($produit->id, [
                'quantite' => $produitData['quantite_stock'] + $produitData['quantite_client'],
                'quantite_stock' => $produitData['quantite_stock'],
                'quantite_client' => $produitData['quantite_client'],
            ]);

            // Calculer la quantité totale (stock + client) et mettre à jour produit_stock
            $quantite_totale = $produitData['quantite_stock'] + $produitData['quantite_client'];

            DB::table('produit_stock')->updateOrInsert(
                [
                    'produit_id' => $produit->id,
                    'stock_id' => $stock->id,
                ],
                [
                    'quantite' => DB::raw("quantite + $quantite_totale") // On incrémente la quantité totale dans produit_stock
                ]
            );
        }

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
