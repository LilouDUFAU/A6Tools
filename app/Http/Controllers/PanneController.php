<?php

namespace App\Http\Controllers;

use App\Models\Panne;
use App\Models\Fournisseur;
use App\Models\Client;
use App\Models\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PanneController extends Controller
{
    // Affiche la liste des pannes
    public function index()
    {
        $pannes = Panne::with(['fournisseur', 'clients', 'actions'])->get();
        return view('gestsav.index', compact('pannes'));
    }

    // Affiche le formulaire de création d'une panne
    public function create()
    {
        $fournisseurs = Fournisseur::all();
        $clients = Client::all();
        $etat_clients = Panne::ETAT_CLIENT;
        return view('gestsav.create', compact('fournisseurs', 'clients', 'etat_clients'));
    }

    // Enregistre une nouvelle panne
    public function store(Request $request)
    {
        Log::info('Début de la méthode store', ['request' => $request->all()]);

        $validated = $request->validate([
            'date_commande' => 'nullable|date',
            'date_panne' => 'required|date',
            'categorie_materiel' => 'required|string',
            'categorie_panne' => 'required|string',
            'detail_panne' => 'required|string',
            'etat' => 'required|in:Ordi de prêt,Échangé,En attente',
            'actions' => 'nullable|array',
            'actions.*' => 'nullable|string|max:255',
        ]);

        Log::info('Validation des données réussie', ['validated' => $validated]);

        // Gestion du fournisseur
        if ($request->filled('new_fournisseur.nom')) {
            $fournisseur = Fournisseur::create([
                'nom' => $request->input('new_fournisseur.nom')
            ]);
            Log::info('Nouveau fournisseur créé', ['fournisseur' => $fournisseur]);
        } elseif ($request->filled('fournisseur_id')) {
            $fournisseur = Fournisseur::find($request->input('fournisseur_id'));
            Log::info('Fournisseur existant sélectionné', ['fournisseur' => $fournisseur]);
        } else {
            $fournisseur = null;
            Log::warning('Aucun fournisseur fourni');
        }

        // Gestion du client
        if ($request->filled('new_client.nom')) {
            $client = Client::create([
                'nom' => $request->input('new_client.nom'),
                'code_client' => $request->input('new_client.code_client'),
            ]);
            Log::info('Nouveau client créé', ['client' => $client]);
        } elseif ($request->filled('client_id')) {
            $client = Client::find($request->input('client_id'));
            Log::info('Client existant sélectionné', ['client' => $client]);
        } else {
            $client = null;
            Log::warning('Aucun client fourni');
        }

        // Création de la panne
        $panne = new Panne();
        $panne->date_commande = $validated['date_commande'] ?? null;
        $panne->date_panne = $validated['date_panne'];
        $panne->categorie_materiel = $validated['categorie_materiel'];
        $panne->categorie_panne = $validated['categorie_panne'];
        $panne->detail_panne = $validated['detail_panne'];
        $panne->etat_client = $validated['etat'];
        if ($fournisseur) {
            $panne->fournisseur_id = $fournisseur->id;
        }
        $panne->save();
        Log::info('Panne créée', ['panne' => $panne]);

        // Association du client à la panne
        if ($client) {
            $panne->clients()->sync([$client->id]);
            Log::info('Client lié à la panne', ['client_id' => $client->id]);
        }

        // Enregistrement des actions associées à la panne (correction ici : on utilise 'intitule')
        if (!empty($validated['actions'])) {
            foreach ($validated['actions'] as $actionIntitule) {
                if (!empty($actionIntitule)) {
                    $panne->actions()->create([
                        'intitule' => $actionIntitule,
                    ]);
                }
            }
            Log::info('Actions enregistrées pour la panne', ['actions' => $validated['actions']]);
        }

        Log::info('Fin de la méthode store');
        return redirect()->route('panne.index')->with('success', 'Panne créée avec succès');
    }

    // Affiche les détails d'une panne
    public function show(string $id)
    {
        $panne = Panne::with(['fournisseur', 'clients', 'actions'])->findOrFail($id);
        return view('gestsav.show', compact('panne'));
    }

    // Affiche le formulaire d'édition d'une panne
    public function edit(string $id)
    {
        $panne = Panne::with('actions')->findOrFail($id);
        $fournisseurs = Fournisseur::all();
        $clients = Client::all();
        $etat_clients = Panne::ETAT_CLIENT;
        $actions = $panne->actions->pluck('intitule')->toArray(); // Récupération des actions existantes
        return view('gestsav.edit', compact('panne', 'fournisseurs', 'clients', 'etat_clients', 'actions'));
    }

    // Met à jour une panne existante
    public function update(Request $request, string $id)
    {
        Log::info('Début de la méthode update', ['request' => $request->all(), 'id' => $id]);

        // Validation des données du formulaire
        $validated = $request->validate([
            'date_commande' => 'nullable|date',
            'date_panne' => 'required|date',
            'categorie_materiel' => 'required|string',
            'categorie_panne' => 'required|string',
            'detail_panne' => 'required|string',
            'client_id' => 'required|exists:clients,id',
            'actions' => 'nullable|array',
            'actions.*' => 'nullable|string|max:255',
        ]);

        // Mise à jour de la panne
        $panne = Panne::findOrFail($id);
        $panne->date_commande = $validated['date_commande'] ?? $panne->date_commande;
        $panne->date_panne = $validated['date_panne'];
        $panne->categorie_materiel = $validated['categorie_materiel'];
        $panne->categorie_panne = $validated['categorie_panne'];
        $panne->detail_panne = $validated['detail_panne'];
        $panne->save();

        // Mise à jour des clients associés à la panne
        $panne->clients()->sync([$validated['client_id']]);

        // Mise à jour des actions existantes et ajout des nouvelles actions
        if (!empty($validated['actions'])) {
            // Filtrer les actions vides ou nulles
            $validActions = array_filter($validated['actions'], function($action) {
                return !empty($action); // Filtre les valeurs nulles et vides
            });

            // Pour chaque action validée
            foreach ($validActions as $actionIntitule) {
                // Vérifie si l'action existe déjà
                $action = $panne->actions()->where('intitule', $actionIntitule)->first();

                if ($action) {
                    // Si l'action existe, mets à jour uniquement `updated_at`
                    $action->touch(); // Cela met à jour le champ `updated_at` sans toucher au `created_at`
                    Log::info('Action mise à jour', ['action' => $action]);
                } else {
                    // Si l'action n'existe pas, crée-la avec `created_at` et `updated_at` égaux
                    $panne->actions()->create([
                        'intitule' => $actionIntitule,
                        'user_id' => auth()->id(), // L'utilisateur connecté effectue l'action
                    ]);
                    Log::info('Nouvelle action créée', ['action_intitule' => $actionIntitule]);
                }
            }
        }

        Log::info('Panne mise à jour', ['panne' => $panne]);
        return redirect()->route('panne.index')->with('success', 'Panne mise à jour avec succès');
    }

    
            
    // Supprime une panne
    public function destroy(string $id)
    {
        $panne = Panne::findOrFail($id);
        $panne->clients()->detach();
        $panne->actions()->delete();
        $panne->delete();
        return redirect()->route('panne.index')->with('success', 'Panne supprimée avec succès');
    }
}
