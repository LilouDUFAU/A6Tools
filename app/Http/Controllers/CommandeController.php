<?php
/**
 * @file CommandeController.php
 * @brief Contrôleur pour la gestion des commandes dans l'application.
 * @version 1.2
 * @date 2025-05-22
 * @author Lilou DUFAU - Modifié pour supporter un fournisseur par produit
 */

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Client;
use App\Models\User;
use App\Models\Produit;
use App\Models\Fournisseur;
use App\Models\PrepAtelier;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
    $userStock = Auth::user()->stock ? Auth::user()->stock->lieux : null;
    $alerteCommandes = [];
    
    foreach ($commandes as $commande) {
        $produits = DB::table('commande_produit')
            ->join('produits', 'commande_produit.produit_id', '=', 'produits.id')
            ->where('commande_produit.commande_id', $commande->id)
            ->select('produits.*') // is_derMinute est maintenant dans la table produits
            ->get();

        foreach ($produits as $produit) {
            if ($commande->date_installation_prevue) {
                $dateInstallation = Carbon::parse($commande->date_installation_prevue);
                
                $condition3 = !$produit->date_livraison_fournisseur && Carbon::now()->addDays(7)->greaterThanOrEqualTo($dateInstallation);
                $condition4 = !$produit->date_livraison_fournisseur && $commande->delai_installation &&
                              Carbon::now()->addDays($commande->delai_installation + 7)->greaterThanOrEqualTo($dateInstallation);
        
                if ($condition3 || $condition4) {
                    $alerteCommandes[$commande->id] = [
                        'commande' => $commande,
                        'produit' => $produit->nom,
                        'dateLivraison' => $produit->date_livraison_fournisseur ? Carbon::parse($produit->date_livraison_fournisseur)->format('d/m/Y') : 'Non renseignée',
                        'dateInstallation' => $dateInstallation->format('d/m/Y'),
                        'difference' => $produit->date_livraison_fournisseur ? $dateInstallation->diffInDays($dateLivraison) : 'N/A',
                    ];
                    break;
                }
            } else {
                Log::debug("Date installation prévue manquante pour commande ID: {$commande->id}");
            }
        }
    }        
    return view('gestcommande.index', compact('commandes', 'alerteCommandes', 'userStock'));
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

        return view('gestcommande.create', compact('clients', 'etats', 'urgences', 'stocks', 'fournisseurs'));
    }

    /**
     * Enregistre une nouvelle commande en base de données avec plusieurs produits et leurs fournisseurs.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * 
     * @throws ValidationException
     * @throws QueryException
     */
    public function store(Request $request)
    {
        Log::info('=== DÉBUT CRÉATION COMMANDE ===');
        Log::info('Données reçues:', $request->all());

        // Validation des champs de base avec fournisseur par produit
        try {
            $validated = $request->validate([
                'numero_commande_fournisseur' => 'required|string|max:255',
                'etat' => 'required|string|max:255',
                'remarque' => 'nullable|string',
                'delai_installation' => 'nullable|integer',
                'date_installation_prevue' => 'nullable|date',
                'reference_devis' => 'nullable|string|max:255',
                'urgence' => 'required|string|max:255',
                'stock_id' => 'required|exists:stocks,id',
                'doc_client' => 'nullable|string|max:255',
                // Validation pour plusieurs produits avec fournisseur individuel
                'produits' => 'required|array|min:1',
                'produits.*.nom' => 'required|string|max:255',
                'produits.*.reference' => 'required|string|max:255',
                'produits.*.prix_referencement' => 'nullable|numeric|min:0',
                'produits.*.lien_produit_fournisseur' => 'nullable|url',
                'produits.*.date_livraison_fournisseur' => 'nullable|date',
                'produits.*.quantite_totale' => 'required|integer|min:1',
                'produits.*.quantite_client' => 'nullable|integer|min:0',
                'produits.*.is_derMinute' => 'nullable|boolean',
                'produits.*.fournisseur_id' => 'nullable|exists:fournisseurs,id',
                'produits.*.new_fournisseur.nom' => 'nullable|string|max:255',
            ]);
            Log::info('Validation réussie');
            Log::info('Données validées:', $validated);
        } catch (ValidationException $e) {
            Log::error('Erreur de validation:', $e->errors());
            throw $e;
        }

        $validated['employe_id'] = auth()->id();
        Log::info('Employe ID ajouté:', ['employe_id' => $validated['employe_id']]);

        try {
            DB::beginTransaction();
            Log::info('Transaction démarrée');

            // Création ou association d'un client
            Log::info('=== GESTION CLIENT ===');
            if ($request->filled('client_id')) {
                $validated['client_id'] = $request->input('client_id');
                Log::info('Client existant sélectionné:', ['client_id' => $validated['client_id']]);
            } elseif ($request->filled('new_client.nom')) {
                Log::info('Création nouveau client:', $request->input('new_client'));
                $client = Client::create([
                    'nom' => $request->input('new_client.nom'),
                    'code_client' => $request->input('new_client.code_client'),
                    'numero_telephone' => $request->input('new_client.numero_telephone'),
                ]);
                $validated['client_id'] = $client->id;
                Log::info('Nouveau client créé:', ['client_id' => $client->id, 'nom' => $client->nom]);
            } else {
                Log::warning('Aucun client sélectionné ou créé');
            }

            // Suppression des données de produits de la validation principale
            unset($validated['produits']);
            Log::info('Données pour création commande:', $validated);
            
            // Création de la commande
            Log::info('=== CRÉATION COMMANDE ===');
            $commande = Commande::create($validated);
            Log::info('Commande créée avec ID:', ['commande_id' => $commande->id]);

            // Traitement des produits multiples avec leurs fournisseurs individuels
            Log::info('=== TRAITEMENT PRODUITS AVEC FOURNISSEURS ===');
            $produitsData = $request->input('produits', []);
            Log::info('Nombre de produits à traiter:', ['count' => count($produitsData)]);
            Log::info('Données produits reçues:', $produitsData);
            
            foreach ($produitsData as $index => $produitData) {
                Log::info("--- Traitement produit $index ---");
                Log::info("Données produit $index:", $produitData);
                
                // Gestion de la checkbox is_derMinute
                $isderMinute = isset($produitData['is_derMinute']) && $produitData['is_derMinute'] ? 1 : 0;
                Log::info("is_derMinute pour produit $index:", ['is_derMinute' => $isderMinute]);
                
                // Données pour création/mise à jour produit
                $produitCreateData = [
                    'nom' => $produitData['nom'],
                    'prix_referencement' => $produitData['prix_referencement'] ?? 0,
                    'lien_produit_fournisseur' => $produitData['lien_produit_fournisseur'] ?? null,
                    'date_livraison_fournisseur' => $produitData['date_livraison_fournisseur'] ?? null,
                    'is_derMinute' => $isderMinute,
                    'updated_at' => now(),
                ];
                Log::info("Données pour création produit $index:", $produitCreateData);
                
                // Création ou mise à jour du produit
                $produit = Produit::updateOrCreate(
                    ['reference' => $produitData['reference']],
                    $produitCreateData
                );
                Log::info("Produit $index créé/mis à jour:", ['produit_id' => $produit->id, 'reference' => $produit->reference]);

                // Gestion du fournisseur pour ce produit spécifique
                Log::info("=== GESTION FOURNISSEUR POUR PRODUIT $index ===");
                $fournisseur_id = null;
                
                if (!empty($produitData['fournisseur_id'])) {
                    $fournisseur_id = $produitData['fournisseur_id'];
                    Log::info("Fournisseur existant sélectionné pour produit $index:", ['fournisseur_id' => $fournisseur_id]);
                } elseif (!empty($produitData['new_fournisseur']['nom'])) {
                    Log::info("Création nouveau fournisseur pour produit $index:", $produitData['new_fournisseur']);
                    $fournisseur = Fournisseur::create([
                        'nom' => $produitData['new_fournisseur']['nom'],
                    ]);
                    $fournisseur_id = $fournisseur->id;
                    Log::info("Nouveau fournisseur créé pour produit $index:", ['fournisseur_id' => $fournisseur->id, 'nom' => $fournisseur->nom]);
                } else {
                    Log::warning("Aucun fournisseur sélectionné ou créé pour produit $index");
                }

                // Création du lien fournisseur-produit-commande si un fournisseur est défini
                if ($fournisseur_id) {
                    Log::info("Vérification lien fournisseur-produit pour produit $index");
                    $exists = DB::table('fournisseur_produit')
                        ->where('fournisseur_id', $fournisseur_id)
                        ->where('produit_id', $produit->id)
                        ->where('commande_id', $commande->id)
                        ->exists();

                    if (!$exists) {
                        $fournisseurProduitData = [
                            'fournisseur_id' => $fournisseur_id,
                            'produit_id' => $produit->id,
                            'commande_id' => $commande->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        DB::table('fournisseur_produit')->insert($fournisseurProduitData);
                        Log::info("Lien fournisseur-produit créé pour produit $index:", $fournisseurProduitData);
                    } else {
                        Log::info("Lien fournisseur-produit existe déjà pour produit $index");
                    }
                } else {
                    Log::warning("Pas de fournisseur_id pour créer le lien avec produit $index");
                }

                // Calcul des quantités
                $quantite_totale = $produitData['quantite_totale'] ?? 0;
                $quantite_client = $produitData['quantite_client'] ?? 0;
                $quantite_stock = $quantite_totale - $quantite_client;
                Log::info("Quantités calculées pour produit $index:", [
                    'quantite_totale' => $quantite_totale,
                    'quantite_client' => $quantite_client,
                    'quantite_stock' => $quantite_stock
                ]);

                // Lier le produit à la commande (pivot)
                $pivotData = [
                    'quantite_totale' => $quantite_totale,
                    'quantite_client' => $quantite_client,
                    'quantite_stock' => $quantite_stock,
                ];
                $commande->produits()->attach($produit->id, $pivotData);
                Log::info("Produit $index attaché à la commande:", $pivotData);

                // Stockage dans la table produit_stock
                $produitStockData = [
                    'produit_id' => $produit->id,
                    'stock_id' => $request->input('stock_id'),
                    'commande_id' => $commande->id,
                    'quantite' => $quantite_stock,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                DB::table('produit_stock')->insert($produitStockData);
                Log::info("Entrée produit_stock créée pour produit $index:", $produitStockData);
            }

            DB::commit();
            Log::info('Transaction validée avec succès');
            Log::info('=== FIN CRÉATION COMMANDE RÉUSSIE ===');

            return redirect()->route('gestcommande.index')->with('success', 'Commande créée avec succès avec ' . count($produitsData) . ' produit(s) et leurs fournisseurs.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('=== ERREUR LORS DE LA CRÉATION ===');
            Log::error('Exception:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withInput()->withErrors(['error' => 'Erreur lors de la création de la commande: ' . $e->getMessage()]);
        }
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

        return view('gestcommande.show', compact('commande'));
    }

    /**
     * Affiche le formulaire d'édition d'une commande.
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

        return view('gestcommande.edit', compact('commande', 'clients', 'stocks', 'etats', 'urgences', 'fournisseurs', 'produits'));
    }

    /**
     * Met à jour une commande existante avec plusieurs produits et leurs fournisseurs.
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
            'numero_commande_fournisseur' => 'required|string|max:255',
            'etat' => 'required|string|max:255',
            'remarque' => 'nullable|string',
            'delai_installation' => 'nullable|integer',
            'date_installation_prevue' => 'nullable|date',
            'reference_devis' => 'nullable|string|max:255',
            'urgence' => 'required|string|max:255',
            'stock_id' => 'required|exists:stocks,id',
            'doc_client' => 'nullable|string|max:255',
            // Validation pour plusieurs produits avec fournisseur individuel
            'produits' => 'required|array|min:1',
            'produits.*.nom' => 'required|string|max:255',
            'produits.*.reference' => 'required|string|max:255',
            'produits.*.prix_referencement' => 'nullable|numeric|min:0',
            'produits.*.lien_produit_fournisseur' => 'nullable|url',
            'produits.*.date_livraison_fournisseur' => 'nullable|date',
            'produits.*.quantite_totale' => 'required|integer|min:1',
            'produits.*.quantite_client' => 'nullable|integer|min:0',
            'produits.*.is_derMinute' => 'nullable|boolean',
            'produits.*.fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'produits.*.new_fournisseur.nom' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $commande = Commande::findOrFail($id);
            $validated['employe_id'] = auth()->id();

            // Suppression des données de produits de la validation principale
            unset($validated['produits']);

            // Mise à jour de la commande
            $commande->update($validated);

            // Mise à jour client
            if ($request->filled('client_id')) {
                $commande->client_id = $request->input('client_id');
            } elseif ($request->filled('new_client.nom')) {
                $client = Client::create([
                    'nom' => $request->input('new_client.nom'),
                    'code_client' => $request->input('new_client.code_client'),
                    'numero_telephone' => $request->input('new_client.numero_telephone'),
                ]);
                $commande->client_id = $client->id;
            }

            // Réinitialisation des produits associés à la commande
            DB::table('produit_stock')->where('commande_id', $commande->id)->delete();
            DB::table('fournisseur_produit')->where('commande_id', $commande->id)->delete();
            $commande->produits()->detach();

            // Traitement des produits multiples avec leurs fournisseurs
            $produitsData = $request->input('produits', []);
            
            foreach ($produitsData as $produitData) {
                // Gestion de la checkbox is_derMinute
                $isderMinute = isset($produitData['is_derMinute']) && $produitData['is_derMinute'] ? 1 : 0;
                
                $produit = Produit::updateOrCreate(
                    ['reference' => $produitData['reference']],
                    [
                        'nom' => $produitData['nom'],
                        'prix_referencement' => $produitData['prix_referencement'] ?? 0,
                        'lien_produit_fournisseur' => $produitData['lien_produit_fournisseur'] ?? null,
                        'date_livraison_fournisseur' => $produitData['date_livraison_fournisseur'] ?? null,
                        'is_derMinute' => $isderMinute,
                        'updated_at' => now(),
                    ]
                );

                // Gestion du fournisseur pour ce produit spécifique
                $fournisseur_id = null;
                if (!empty($produitData['fournisseur_id'])) {
                    $fournisseur_id = $produitData['fournisseur_id'];
                } elseif (!empty($produitData['new_fournisseur']['nom'])) {
                    $fournisseur = Fournisseur::create([
                        'nom' => $produitData['new_fournisseur']['nom'],
                    ]);
                    $fournisseur_id = $fournisseur->id;
                }

                // Lien fournisseur-produit-commande
                if ($fournisseur_id) {
                    DB::table('fournisseur_produit')->insert([
                        'fournisseur_id' => $fournisseur_id,
                        'produit_id' => $produit->id,
                        'commande_id' => $commande->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Calcul des quantités
                $quantite_totale = $produitData['quantite_totale'] ?? 0;
                $quantite_client = $produitData['quantite_client'] ?? 0;
                $quantite_stock = $quantite_totale - $quantite_client;

                // Liaison produit-commande
                $commande->produits()->attach($produit->id, [
                    'quantite_totale' => $quantite_totale,
                    'quantite_client' => $quantite_client,
                    'quantite_stock' => $quantite_stock,
                ]);

                // Stockage produit-stock
                DB::table('produit_stock')->insert([
                    'produit_id' => $produit->id,
                    'stock_id' => $request->input('stock_id'),
                    'commande_id' => $commande->id,
                    'quantite' => $quantite_stock,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $commande->save();
            DB::commit();

            return redirect()->route('gestcommande.show', $commande->id)->with('success', 'Commande mise à jour avec succès avec ' . count($produitsData) . ' produit(s) et leurs fournisseurs.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour de la commande: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Erreur lors de la mise à jour de la commande: ' . $e->getMessage()]);
        }
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
        try {
            DB::beginTransaction();
            
            $commande = Commande::findOrFail($id);

            // Suppression des entités liées
            PrepAtelier::where('commande_id', $commande->id)->delete();
            $commande->produits()->detach();
            DB::table('produit_stock')->where('commande_id', $commande->id)->delete();
            DB::table('fournisseur_produit')->where('commande_id', $commande->id)->delete();

            // Suppression de la commande
            $commande->delete();
            
            DB::commit();

            return redirect()->route('gestcommande.index')->with('success', 'Commande et ses préparations associées supprimées avec succès.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression de la commande: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la suppression de la commande.']);
        }
    }

    public function updateEtat(Request $request, Commande $commande)
    {
        $validated = $request->validate([
            'etat' => 'required|in:a faire,commandé,reçu,prévenu,délais'
        ]);

        $commande->update(['etat' => $validated['etat']]);

        return response()->json([
            'success' => true,
            'message' => 'État mis à jour avec succès'
        ]);
    }

    /**
     * Mettre à jour le fournisseur d'un produit spécifique dans une commande
     */
    public function updateFournisseurProduit(Request $request)
    {
        // Valider les données entrantes
        $validatedData = $request->validate([
            'fournisseur' => 'required|string|max:255',
            'commande_id' => 'required|exists:commandes,id',
            'produit_id' => 'required|exists:produits,id'
        ]);

        try {
            // Commencer une transaction de base de données
            DB::beginTransaction();

            // Récupérer la commande et le produit
            $commande = Commande::findOrFail($request->input('commande_id'));
            $produit = Produit::findOrFail($request->input('produit_id'));

            // Récupérer ou créer le fournisseur
            $fournisseur = Fournisseur::firstOrCreate(
                ['nom' => $validatedData['fournisseur']]
            );

            // Supprimer l'ancien lien fournisseur-produit pour cette commande et ce produit
            DB::table('fournisseur_produit')
                ->where('commande_id', $commande->id)
                ->where('produit_id', $produit->id)
                ->delete();

            // Créer le nouveau lien fournisseur-produit
            DB::table('fournisseur_produit')->insert([
                'fournisseur_id' => $fournisseur->id,
                'produit_id' => $produit->id,
                'commande_id' => $commande->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Valider la transaction
            DB::commit();

            // Retourner une réponse JSON de succès
            return response()->json([
                'message' => 'Fournisseur mis à jour avec succès pour le produit',
                'fournisseur' => $fournisseur->nom,
                'produit' => $produit->nom
            ]);

        } catch (\Exception $e) {
            // En cas d'erreur, annuler la transaction
            DB::rollBack();

            // Retourner une réponse d'erreur
            return response()->json([
                'message' => 'Impossible de mettre à jour le fournisseur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour le fournisseur de tous les produits d'une commande (méthode de compatibilité)
     */
    public function updateFournisseur(Request $request)
    {
        // Valider les données entrantes
        $validatedData = $request->validate([
            'fournisseur' => 'required|string|max:255',
            'commande_id' => 'required|exists:commandes,id'
        ]);

        try {
            // Commencer une transaction de base de données
            DB::beginTransaction();

            // Récupérer la commande
            $commande = Commande::findOrFail($request->input('commande_id'));

            // Récupérer ou créer le fournisseur
            $fournisseur = Fournisseur::firstOrCreate(
                ['nom' => $validatedData['fournisseur']]
            );

            // Récupérer tous les produits de la commande
            $produits = $commande->produits;

            if ($produits->count() > 0) {
                // Supprimer les liens fournisseur-produit existants pour cette commande
                DB::table('fournisseur_produit')
                    ->where('commande_id', $commande->id)
                    ->delete();

                // Créer de nouveaux liens fournisseur-produit pour tous les produits
                foreach ($produits as $produit) {
                    DB::table('fournisseur_produit')->insert([
                        'fournisseur_id' => $fournisseur->id,
                        'produit_id' => $produit->id,
                        'commande_id' => $commande->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // Valider la transaction
            DB::commit();

            // Retourner une réponse JSON de succès
            return response()->json([
                'message' => 'Fournisseur mis à jour avec succès pour tous les produits',
                'fournisseur' => $fournisseur->nom,
                'produits_count' => $produits->count()
            ]);

        } catch (\Exception $e) {
            // En cas d'erreur, annuler la transaction
            DB::rollBack();

            // Retourner une réponse d'erreur
            return response()->json([
                'message' => 'Impossible de mettre à jour le fournisseur',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}