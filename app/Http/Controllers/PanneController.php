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
        $pannes = Panne::with(['fournisseur', 'clients'])->get();
        return view('gestsav.index', compact('pannes'));
    }

    // Affiche le formulaire de création d'une panne
    public function create()
    {
        $fournisseurs = Fournisseur::all();
        $clients = Client::all();
        $etat_clients = Panne::ETAT_CLIENT;
        $statut = Panne::STATUT;
        return view('gestsav.create', compact('fournisseurs', 'clients', 'etat_clients', 'statut'));
    }

    // Enregistre une nouvelle panne
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'date_commande' => 'nullable|date',
                'date_panne' => 'nullable|date',
                'categorie_materiel' => 'nullable|string',
                'categorie_panne' => 'nullable|string',
                'detail_panne' => 'nullable|string',
                'etat' => 'required|in:Ordi de prêt,Échangé,En attente',
                'demande' => 'required|string',
                'numero_sav' => 'nullable|string',
                'statut' => 'required|in:En attente,Remboursement,Transit,Envoyé,Échange anticipé',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Échec de la validation', ['errors' => $e->errors()]);
            throw $e;
        }

        // Gestion du fournisseur
        if ($request->filled('new_fournisseur.nom')) {
            $fournisseur = Fournisseur::create([
                'nom' => $request->input('new_fournisseur.nom')
            ]);
        } elseif ($request->filled('fournisseur_id')) {
            $fournisseur = Fournisseur::find($request->input('fournisseur_id'));
        } else {
            $fournisseur = null;
            Log::warning('Aucun fournisseur spécifié');
        }

        // Gestion du client
        if ($request->filled('new_client.nom')) {
            $client = Client::create([
                'nom' => $request->input('new_client.nom'),
                'code_client' => $request->input('new_client.code_client'),
                'numero_telephone' => $request->input('new_client.numero_telephone'),
            ]);
        } elseif ($request->filled('client_id')) {
            $client = Client::find($request->input('client_id'));
        } else {
            $client = null;
            Log::warning('Aucun client spécifié');
        }

        // Création de la panne
        try {
            $panne = new Panne();
            $panne->numero_sav = $validated['numero_sav'] ?? null;
            $panne->date_commande = $validated['date_commande'] ?? null;
            $panne->date_panne = $validated['date_panne'] ?? null;
            $panne->categorie_materiel = $validated['categorie_materiel'] ?? null;
            $panne->categorie_panne = $validated['categorie_panne'] ?? null;
            $panne->detail_panne = $validated['detail_panne'] ?? null;
            $panne->etat_client = $validated['etat'];
            $panne->demande = $validated['demande'];
            $panne->statut = $validated['statut'];

            if ($fournisseur) {
                $panne->fournisseur_id = $fournisseur->id;
            }

            $panne->save();
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la panne', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la création de la panne');
        }

        // Association du client à la panne
        if ($client) {
            try {
                $panne->clients()->sync([$client->id]);
            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'association du client à la panne', [
                    'error' => $e->getMessage(),
                    'client_id' => $client->id,
                    'panne_id' => $panne->id
                ]);
            }
        } else {
            Log::warning('Aucun client à associer à la panne');
        }

        return redirect()->route('gestsav.index')->with('success', 'Panne créée avec succès');
    }
    
    // Affiche les détails d'une panne
    public function show(string $id)
    {
        $panne = Panne::with(['fournisseur', 'clients'])->findOrFail($id);
        return view('gestsav.show', compact('panne'));
    }

    // Affiche le formulaire d'édition d'une panne
    public function edit(string $id)
    {
        $panne = Panne::with('actions')->findOrFail($id);
        $fournisseurs = Fournisseur::all();
        $clients = Client::all();
        $etat_clients = Panne::ETAT_CLIENT;
        $statut = Panne::STATUT;
        return view('gestsav.edit', compact('panne', 'fournisseurs', 'clients', 'etat_clients', 'statut'));
    }

    // Met à jour une panne existante
    public function update(Request $request, string $id)
    {
        // Validation des données du formulaire
        $validated = $request->validate([
            'numero_sav' => 'nullable|string',
            'date_commande' => 'nullable|date',
            'date_panne' => 'nullable|date',
            'categorie_materiel' => 'nullable|string',
            'categorie_panne' => 'nullable|string',
            'detail_panne' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
            'etat' => 'required|in:Ordi de prêt,Échangé,En attente',
            'demande' => 'nullable|string',
            'statut' => 'required|in:En attente,Remboursement,Transit,Envoyé,Échange anticipé',    
        ]);

        // Récupérer la panne
        $panne = Panne::findOrFail($id);

        // Mettre à jour les champs principaux
        $panne->update([
            'numero_sav' => $validated['numero_sav'],
            'date_commande' => $validated['date_commande'] ?? $panne->date_commande,
            'date_panne' => $validated['date_panne'],
            'categorie_materiel' => $validated['categorie_materiel'],
            'categorie_panne' => $validated['categorie_panne'],
            'detail_panne' => $validated['detail_panne'],
            'demande' => $validated['demande'] ?? $panne->demande,
            'etat_client' => $validated['etat'],
            'statut' => $validated['statut'],
        ]);

        // Mettre à jour l'association client
        $panne->clients()->sync([$validated['client_id']]);

        return redirect()->route('gestsav.index')->with('success', 'Panne mise à jour avec succès');
    }
            
    // Supprime une panne
    public function destroy(string $id)
    {
        $panne = Panne::findOrFail($id);
        $panne->clients()->detach();
        // $panne->actions()->delete();
        $panne->delete();
        return redirect()->route('gestsav.index')->with('success', 'Panne supprimée avec succès');
    }


    public function updateSav(Request $request, $id)
    {
        $request->validate([
            'numero_sav' => 'required|string|max:255'
        ]);

        $panne = Panne::findOrFail($id);
        $panne->update(['numero_sav' => $request->numero_sav]);

        return response()->json(['success' => true]);
    }


}
