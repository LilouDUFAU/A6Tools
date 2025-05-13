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
        // Log des données reçues avant la validation
        Log::info('Données reçues avant validation', ['request_data' => $request->all()]);
    
        // Validation des données
        Log::info('Début de la validation des données', ['request_data' => $request->all()]);
    
        $validated = $request->validate([
            'date_commande' => 'nullable|date',
            'date_panne' => 'required|date',
            'categorie_materiel' => 'required|string',
            'categorie_panne' => 'required|string',
            'detail_panne' => 'required|string',
            'etat' => 'required|in:Ordi de prêt,Échangé,En attente',
            'demande' => 'nullable|string',
            'numero_sav' => 'nullable|string',
            'statut' => 'required|in:Remboursement,Transit,Envoyé,Échange anticipé',
        ]);
    
        Log::info('Données validées', ['validated_data' => $validated]);
    
        // Gestion du fournisseur
        if ($request->filled('new_fournisseur.nom')) {
            $fournisseur = Fournisseur::create([
                'nom' => $request->input('new_fournisseur.nom')
            ]);
            Log::info('Fournisseur créé', ['fournisseur' => $fournisseur]);
        } elseif ($request->filled('fournisseur_id')) {
            $fournisseur = Fournisseur::find($request->input('fournisseur_id'));
            Log::info('Fournisseur existant trouvé', ['fournisseur' => $fournisseur]);
        } else {
            $fournisseur = null;
            Log::info('Aucun fournisseur spécifié');
        }
    
        // Gestion du client
        if ($request->filled('new_client.nom')) {
            $client = Client::create([
                'nom' => $request->input('new_client.nom'),
                'code_client' => $request->input('new_client.code_client'),
            ]);
            Log::info('Client créé', ['client' => $client]);
        } elseif ($request->filled('client_id')) {
            $client = Client::find($request->input('client_id'));
            Log::info('Client existant trouvé', ['client' => $client]);
        } else {
            $client = null;
            Log::info('Aucun client spécifié');
        }
    
        // Création de la panne
        try {
            $panne = new Panne();
            $panne->numero_sav = $validated['numero_sav'];
            $panne->date_commande = $validated['date_commande'] ?? null;
            $panne->date_panne = $validated['date_panne'];
            $panne->categorie_materiel = $validated['categorie_materiel'];
            $panne->categorie_panne = $validated['categorie_panne'];
            $panne->detail_panne = $validated['detail_panne'];
            $panne->etat_client = $validated['etat'];
            $panne->demande = $validated['demande'] ?? null;
            $panne->statut = $validated['statut'];
            if ($fournisseur) {
                $panne->fournisseur_id = $fournisseur->id;
            }
            $panne->save();
            Log::info('Panne créée', ['panne' => $panne]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la panne', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la création de la panne');
        }
    
        // Association du client à la panne
        if ($client) {
            try {
                $panne->clients()->sync([$client->id]);
                Log::info('Client associé à la panne', ['client_id' => $client->id, 'panne_id' => $panne->id]);
            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'association du client à la panne', ['error' => $e->getMessage()]);
            }
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
        // $actions = $panne->actions->pluck('intitule')->toArray(); // Récupération des actions existantes
        $statut = Panne::STATUT;
        return view('gestsav.edit', compact('panne', 'fournisseurs', 'clients', 'etat_clients', 'statut'));
    }

    // Met à jour une panne existante
// Met à jour une panne existante
public function update(Request $request, string $id)
{
    // Validation des données du formulaire
    $validated = $request->validate([
        'numero_sav' => 'nullable|string',
        'date_commande' => 'nullable|date',
        'date_panne' => 'required|date',
        'categorie_materiel' => 'required|string',
        'categorie_panne' => 'required|string',
        'detail_panne' => 'required|string',
        'client_id' => 'required|exists:clients,id',
        'demande' => 'nullable|string',
        'statut' => 'required|in:Remboursement,Transit,Envoyé,Échange anticipé',
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
