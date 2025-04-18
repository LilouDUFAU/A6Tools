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
        $fournisseurs = Fournisseur::all();
        $etats = Commande::ETATS;
        $urgences = Commande::URGENCES;
        $stocks = Stock::LIEUX;

        return view('gestock.create', compact('clients', 'etats', 'urgences', 'stocks', 'fournisseurs'));
    }

    /**
     * Enregistre une nouvelle commande.
     */
    public function store(Request $request)
    {
        // Validation basique
        $validated = $request->validate([
            'etat' => 'required|string|max:255',
            'remarque' => 'nullable|string',
            'delai_installation' => 'nullable|integer',
            'date_installation_prevue' => 'nullable|date',
            'reference_devis' => 'nullable|string|max:255',
            'urgence' => 'required|string|max:255',
            'stock_id' => 'required|exists:stocks,id',
        ]);

        $validated['employe_id'] = auth()->id();

        // CLIENT
        if ($request->filled('client_id')) {
            $validated['client_id'] = $request->input('client_id');
        } elseif ($request->filled('new_client.nom')) {
            $client = Client::create([
                'nom' => $request->input('new_client.nom'),
                'code_client' => $request->input('new_client.code_client'),
            ]);
            $validated['client_id'] = $client->id;
        }

        // COMMANDE
        $commande = Commande::create($validated);

        // FOURNISSEUR
        $fournisseur_id = null;
        if ($request->filled('fournisseur_id')) {
            $fournisseur_id = $request->input('fournisseur_id');
        } elseif ($request->filled('new_fournisseur.nom')) {
            $fournisseur = Fournisseur::create([
                'nom' => $request->input('new_fournisseur.nom'),
            ]);
            $fournisseur_id = $fournisseur->id;
        }

        // PRODUIT
        $produitData = $request->input('produit');
        if (!empty($produitData)) {
            $produit = Produit::firstOrCreate(
                ['reference' => $produitData['reference']],
                [
                    'nom' => $produitData['nom'],
                    'prix_referencement' => $produitData['prix_referencement'] ?? 0,
                    'lien_produit_fournisseur' => $produitData['lien_produit_fournisseur'] ?? null,
                    'date_livraison_fournisseur' => $produitData['date_livraison_fournisseur'] ?? null,
                ]
            );

            // Attacher fournisseur au produit
            if ($fournisseur_id) {
                $produit->fournisseurs()->syncWithoutDetaching([
                    $fournisseur_id => ['commande_id' => $commande->id]
                ]);
            }

            // Attacher produit à la commande
            $commande->produits()->attach($produit->id, [
                'quantite_totale' => $produitData['quantite_totale'] ?? 0,
                'quantite_client' => $produitData['quantite_client'] ?? 0,
                'quantite_stock' => ($produitData['quantite_totale'] ?? 0) - ($produitData['quantite_client'] ?? 0),
            ]);

            // Enregistrer dans produit_stock
            DB::table('produit_stock')->insert([
                'produit_id' => $produit->id,
                'stock_id' => $request->input('stock_id'),
                'commande_id' => $commande->id,
                'quantite' => $produitData['quantite_totale'] ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('commande.index')->with('success', 'Commande créée avec succès.');
    }



    /**
     * Affiche les détails d'une commande.
     */
    public function show(string $id)
    {
        // Récupère la commande avec les relations client, employé et produits associés
        $commande = Commande::with(['client', 'employe', 'produits.fournisseurs', 'produits.stocks'])->findOrFail($id);
        
        // Retourner la vue avec la commande et ses relations
        return view('gestock.show', compact('commande'));
    }

    /**
     * Affiche le formulaire d'édition d'une commande.
     */
    public function edit(string $id)
    {
        // Récupérer la commande avec ses produits et son client
        $commande = Commande::with(['produits' => function($query) {
            $query->withPivot('quantite_totale', 'quantite_client', 'quantite_stock');
        }, 'client'])->findOrFail($id);
        
        // Récupérer tous les clients, fournisseurs, stocks et produits disponibles
        $clients = Client::all();
        $stocks = Stock::all();
        $fournisseurs = Fournisseur::all();
        $produits = Produit::all(); // Si vous avez besoin de lister tous les produits

        // Récupérer les autres données pour le formulaire
        $etats = Commande::ETATS;
        $urgences = Commande::URGENCES;

        // Renvoyer la vue avec les données nécessaires
        return view('gestock.edit', compact('commande', 'clients', 'stocks', 'etats', 'urgences', 'fournisseurs', 'produits'));
    }


    /**
     * Met à jour une commande existante.
     */
    public function update(Request $request, $id)
    {
        // Validation des données de base
        $validated = $request->validate([
            'etat' => 'required|string|max:255',
            'remarque' => 'nullable|string',
            'delai_installation' => 'nullable|integer',
            'date_installation_prevue' => 'nullable|date',
            'reference_devis' => 'nullable|string|max:255',
            'urgence' => 'required|string|max:255',
            'stock_id' => 'required|exists:stocks,id',
        ]);

        $commande = Commande::findOrFail($id);
        $validated['employe_id'] = auth()->id();
        $commande->update($validated);

        // CLIENT
        if ($request->filled('client_id')) {
            // Si un client est sélectionné, on le met à jour
            $client = Client::findOrFail($request->input('client_id'));
            $client->update([
                'nom' => $request->input('new_client.nom', $client->nom),
                'code_client' => $request->input('new_client.code_client', $client->code_client),
            ]);
            $commande->client_id = $client->id;
        } elseif ($request->filled('new_client.nom')) {
            // Si un nouveau client est fourni, on le crée
            $client = Client::create([
                'nom' => $request->input('new_client.nom'),
                'code_client' => $request->input('new_client.code_client'),
            ]);
            $commande->client_id = $client->id;
        }

        // Fournisseur
        $fournisseur_id = null;
        if ($request->filled('fournisseur_id')) {
            // Si un fournisseur est sélectionné, on le met à jour
            $fournisseur = Fournisseur::findOrFail($request->input('fournisseur_id'));
            $fournisseur->update([
                'nom' => $request->input('new_fournisseur.nom', $fournisseur->nom),
            ]);
            $fournisseur_id = $fournisseur->id;
        } elseif ($request->filled('new_fournisseur.nom')) {
            // Si un nouveau fournisseur est fourni, on le crée
            $fournisseur = Fournisseur::create([
                'nom' => $request->input('new_fournisseur.nom'),
            ]);
            $fournisseur_id = $fournisseur->id;
        }

        // Supprimer les anciennes données liées au produit
        DB::table('produit_stock')->where('commande_id', $commande->id)->delete();
        $commande->produits()->detach();

        // PRODUIT (1 seul)
        $produitData = $request->input('produit');
        if (!empty($produitData)) {
            $produit = Produit::firstOrCreate(
                ['reference' => $produitData['reference']],
                [
                    'nom' => $produitData['nom'],
                    'prix_referencement' => $produitData['prix_referencement'] ?? 0,
                    'lien_produit_fournisseur' => $produitData['lien_produit_fournisseur'] ?? null,
                    'date_livraison_fournisseur' => $produitData['date_livraison_fournisseur'] ?? null,
                ]
            );

            // Lier le fournisseur (si fourni)
            if ($fournisseur_id) {
                $produit->fournisseurs()->syncWithoutDetaching([
                    $fournisseur_id => ['commande_id' => $commande->id]
                ]);
            }

            // Lier le produit à la commande (1 produit)
            $commande->produits()->attach($produit->id, [
                'quantite_totale' => $produitData['quantite_totale'] ?? 0,
                'quantite_client' => $produitData['quantite_client'] ?? 0,
                'quantite_stock' => ($produitData['quantite_totale'] ?? 0) - ($produitData['quantite_client'] ?? 0),
            ]);

            // Insérer dans produit_stock
            DB::table('produit_stock')->insert([
                'produit_id' => $produit->id,
                'stock_id' => $request->input('stock_id'),
                'commande_id' => $commande->id,
                'quantite' => $produitData['quantite_totale'] ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Sauvegarder la commande après modifications
        $commande->save();

        return redirect()->route('commande.index')->with('success', 'Commande mise à jour avec succès.');
    }


    /**
     * Supprime une commande.
     */
    public function destroy(string $id)
    {
        $commande = Commande::findOrFail($id);

        // Détacher tous les produits de la commande (table commande_produit)
        $commande->produits()->detach();

        // Supprimer les entrées dans produit_stock associées à cette commande
        DB::table('produit_stock')->where('commande_id', $commande->id)->delete();

        // Supprimer la commande elle-même
        $commande->delete();

        return redirect()->route('commande.index')->with('success', 'Commande supprimée avec succès.');
    }
}