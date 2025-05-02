<?php
/**
 * @file CommandeController.php
 * @brief Contrôleur pour la gestion des commandes dans l'application.
 * @version 1.0
 * @date 2025-04-18
 * @author Lilou DUFAU
 */

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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

/**
 * @class CommandeController
 * @brief Contrôleur pour les opérations CRUD sur les commandes.
 */
class CommandeController extends Controller
{
    /**
     * Affiche la liste des commandes.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $commandes = Commande::with(['client', 'employe'])->get();
        return view('gestock.index', compact('commandes'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle commande.
     * 
     * @return \Illuminate\View\View
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
     * Enregistre une nouvelle commande en base de données.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * 
     * @throws ValidationException
     * @throws QueryException
     */
    public function store(Request $request)
    {
        // Validation des champs de base
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

        // Création ou association d’un client
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

        // Création ou association d’un fournisseur
        $fournisseur_id = null;
        if ($request->filled('fournisseur_id')) {
            $fournisseur_id = $request->input('fournisseur_id');
        } elseif ($request->filled('new_fournisseur.nom')) {
            $fournisseur = Fournisseur::create([
                'nom' => $request->input('new_fournisseur.nom'),
            ]);
            $fournisseur_id = $fournisseur->id;
        }

        // Traitement du produit
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

            // Création du lien fournisseur-produit-commande si inexistant
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

            // Lier le produit à la commande (pivot)
            $commande->produits()->attach($produit->id, [
                'quantite_totale' => $produitData['quantite_totale'] ?? 0,
                'quantite_client' => $produitData['quantite_client'] ?? 0,
                'quantite_stock' => ($produitData['quantite_totale'] ?? 0) - ($produitData['quantite_client'] ?? 0),
            ]);

            // Stockage dans la table produit_stock
            DB::table('produit_stock')->insert([
                'produit_id' => $produit->id,
                'stock_id' => $request->input('stock_id'),
                'commande_id' => $commande->id,
                'quantite' => ($produitData['quantite_totale'] ?? 0) - ($produitData['quantite_client'] ?? 0),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('commande.index')->with('success', 'Commande créée avec succès.');
    }

    /**
     * Affiche les détails d'une commande.
     * 
     * @param string $id
     * @return \Illuminate\View\View
     * 
     * @throws ModelNotFoundException
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
     * Affiche le formulaire d’édition d’une commande.
     * 
     * @param string $id
     * @return \Illuminate\View\View
     * 
     * @throws ModelNotFoundException
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
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     * 
     * @throws ValidationException
     * @throws ModelNotFoundException
     * @throws QueryException
     * @throws Exception
     */
    public function update(Request $request, $id)
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

        $commande = Commande::findOrFail($id);
        $validated['employe_id'] = auth()->id();

        $commande->update($validated);

        // Mise à jour client
        if ($request->filled('client_id')) {
            $commande->client_id = $request->input('client_id');
        } elseif ($request->filled('new_client.nom')) {
            $client = Client::create([
                'nom' => $request->input('new_client.nom'),
                'code_client' => $request->input('new_client.code_client'),
            ]);
            $commande->client_id = $client->id;
        }

        // Fournisseur (inchangé si rien n’est sélectionné)
        $fournisseur_id = $request->input('fournisseur_id', $commande->fournisseur_id);

        // Réinitialisation des produits associés à la commande
        DB::table('produit_stock')->where('commande_id', $commande->id)->delete();
        $commande->produits()->detach();

        // Produit unique
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

            DB::table('fournisseur_produit')
                ->where('produit_id', $produit->id)
                ->where('commande_id', $commande->id)
                ->delete();

            DB::table('fournisseur_produit')->insert([
                'fournisseur_id' => $fournisseur_id,
                'produit_id' => $produit->id,
                'commande_id' => $commande->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

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

        $commande->save();

        return redirect()->route('commande.index')->with('success', 'Commande mise à jour avec succès.');
    }

    /**
     * Supprime une commande et ses dépendances.
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     * 
     * @throws ModelNotFoundException
     * @throws QueryException
     * @throws Exception
     */
    public function destroy(string $id)
    {
        $commande = Commande::findOrFail($id);

        // Suppression des entités liées
        PrepAtelier::where('commande_id', $commande->id)->delete();
        $commande->produits()->detach();
        DB::table('produit_stock')->where('commande_id', $commande->id)->delete();

        // Suppression de la commande
        $commande->delete();

        return redirect()->route('commande.index')->with('success', 'Commande et ses préparations associées supprimées avec succès.');
    }
}
