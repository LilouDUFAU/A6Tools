<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Client;
use App\Models\User;
use App\Models\Produit;
use App\Models\Fournisseur;
use App\Models\PrepAtelier;
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

        if ($request->filled('client_id')) {
            $validated['client_id'] = $request->input('client_id');
        } elseif ($request->filled('new_client.nom')) {
            $client = Client::create([
                'nom' => $request->input('new_client.nom'),
                'code_client' => $request->input('new_client.code_client'),
            ]);
            $validated['client_id'] = $client->id;
        }

        $commande = Commande::create($validated);

        $fournisseur_id = null;
        if ($request->filled('fournisseur_id')) {
            $fournisseur_id = $request->input('fournisseur_id');
        } elseif ($request->filled('new_fournisseur.nom')) {
            $fournisseur = Fournisseur::create([
                'nom' => $request->input('new_fournisseur.nom'),
            ]);
            $fournisseur_id = $fournisseur->id;
        }

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

            if ($fournisseur_id) {
                $exists = DB::table('fournisseur_produit')
                    ->where('fournisseur_id', $fournisseur_id)
                    ->where('produit_id', $produit->id)
                    ->where('commande_id', $commande->id)
                    ->exists();

                if (!$exists) {
                    DB::table('fournisseur_produit')->insert([
                        'fournisseur_id' => $fournisseur_id,
                        'produit_id' => $produit->id,
                        'commande_id' => $commande->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $commande->produits()->attach($produit->id, [
                'quantite_totale' => $produitData['quantite_totale'] ?? 0,
                'quantite_client' => $produitData['quantite_client'] ?? 0,
                'quantite_stock' => ($produitData['quantite_totale'] ?? 0) - ($produitData['quantite_client'] ?? 0),
            ]);

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
        $commande = Commande::with([
            'client', 
            'employe', 
            'produits.fournisseurs', 
            'produits.stocks', 
            'preparation'
        ])->findOrFail($id);

        return view('gestock.show', compact('commande'));
    }

    /**
     * Affiche le formulaire d'édition d'une commande.
     */
    public function edit(string $id)
    {

        $commande = Commande::with(['produits' => function($query) {
            $query->withPivot('quantite_totale', 'quantite_client', 'quantite_stock');
        }, 'client'])->findOrFail($id);


        $clients = Client::all();
        $stocks = Stock::all();
        $fournisseurs = Fournisseur::all();
        $produits = Produit::all(); 

        $etats = Commande::ETATS;
        $urgences = Commande::URGENCES;

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
    
        // Mise à jour de la commande avec les données validées
        $commande->update($validated);
    
        // CLIENT
        if ($request->filled('client_id')) {
            // Si un client est sélectionné, l'associer à la commande
            $commande->client_id = $request->input('client_id');
        } elseif ($request->filled('new_client.nom')) {
            // Si un nouveau client est ajouté
            $clientNom = $request->input('new_client.nom');
            $clientCodeClient = $request->input('new_client.code_client');
    
            if ($clientNom && $clientCodeClient) {
                $client = Client::create([
                    'nom' => $clientNom,
                    'code_client' => $clientCodeClient,
                ]);
                $commande->client_id = $client->id;
            } else {
                $commande->client_id = null; // Si le client n'est pas valide, le laisser à null
            }
        }
    
        // Fournisseur : Si aucune modification n'est faite, rien ne se passe
        $fournisseur_id = $commande->fournisseur_id; // Garde le fournisseur existant par défaut
    
        if ($request->filled('fournisseur_id')) {
            // Si un autre fournisseur est sélectionné, on utilise simplement son ID sans modifier son nom
            $fournisseur_id = $request->input('fournisseur_id');
            
            // On ne modifie pas le nom du fournisseur sélectionné
        }
    
        // Supprimer les anciennes données liées au produit
        DB::table('produit_stock')->where('commande_id', $commande->id)->delete();
        $commande->produits()->detach();
    
        // PRODUIT (1 seul)
        $produitData = $request->input('produit');
        if (!empty($produitData)) {
            $produit = Produit::updateOrCreate(
                ['reference' => $produitData['reference']],
                [
                    'nom' => $produitData['nom'],
                    'prix_referencement' => $produitData['prix_referencement'] ?? 0,
                    'lien_produit_fournisseur' => $produitData['lien_produit_fournisseur'] ?? null,
                    'date_livraison_fournisseur' => $produitData['date_livraison_fournisseur'] ?? null,
                ]
            );
    
            // Lier le fournisseur (si sélectionné ou modifié)
            if ($fournisseur_id) {
                // Supprimer d'abord toute relation existante pour cette commande et ce produit
                DB::table('fournisseur_produit')
                    ->where('produit_id', $produit->id)
                    ->where('commande_id', $commande->id)
                    ->delete();
                    
                // Créer la nouvelle relation avec le fournisseur sélectionné
                DB::table('fournisseur_produit')->insert([
                    'fournisseur_id' => $fournisseur_id,
                    'produit_id' => $produit->id,
                    'commande_id' => $commande->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
    
            // Attacher le produit à la commande avec ses quantités
            $commande->produits()->attach($produit->id, [
                'quantite_totale' => $produitData['quantite_totale'] ?? 0,
                'quantite_client' => $produitData['quantite_client'] ?? 0,
                'quantite_stock' => ($produitData['quantite_totale'] ?? 0) - ($produitData['quantite_client'] ?? 0),
            ]);
    
            // Mettre à jour le stock
            DB::table('produit_stock')->insert([
                'produit_id' => $produit->id,
                'stock_id' => $request->input('stock_id'),
                'commande_id' => $commande->id,
                'quantite' => $produitData['quantite_totale'] ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    
        // Sauvegarde des modifications
        $commande->save();
    
        return redirect()->route('commande.index')->with('success', 'Commande mise à jour avec succès.');
    }
        

    
    /**
     * Supprime une commande.
     */
    public function destroy(string $id)
    {
        $commande = Commande::findOrFail($id);

        // Supprimer les préparations associées à la commande
        PrepAtelier::where('commande_id', $commande->id)->delete();

        // Détacher les produits associés à la commande
        $commande->produits()->detach();

        // Supprimer les entrées dans produit_stock liées à la commande
        DB::table('produit_stock')->where('commande_id', $commande->id)->delete();

        // Supprimer la commande
        $commande->delete();

        return redirect()->route('commande.index')->with('success', 'Commande et ses préparations associées supprimées avec succès.');
    }
}
