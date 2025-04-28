<?php

namespace App\Http\Controllers;

use App\Models\Panne;
use App\Models\Fournisseur;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PanneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pannes = Panne::with(['fournisseur', 'clients'])->get();
        return view('gestsav.index', compact('pannes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fournisseurs = Fournisseur::all();
        $clients = Client::all();
        $etat_clients = Panne::ETAT_CLIENT;
        return view('gestsav.create', compact('fournisseurs', 'clients', 'etat_clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
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
    
        // Liaison Client-Panne
        if ($client) {
            $panne->clients()->sync([$client->id]);
            Log::info('Client lié à la panne', ['client_id' => $client->id]);
        }
    
        Log::info('Fin de la méthode store');
    
        return redirect()->route('panne.index')->with('success', 'Panne créée avec succès');
    }
    
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $panne = Panne::with(['fournisseur', 'clients'])->findOrFail($id);
        return view('gestsav.show', compact('panne'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $panne = Panne::findOrFail($id);
        $fournisseurs = Fournisseur::all();
        $clients = Client::all();
        $etat_clients = Panne::ETAT_CLIENT;
        return view('gestsav.edit', compact('panne', 'fournisseurs', 'clients', 'etat_clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Log::info('Début de la méthode update', ['request' => $request->all(), 'id' => $id]);
    
        // Validation des données
        $validated = $request->validate([
            'date_commande' => 'nullable|date',
            'date_panne' => 'required|date',
            'categorie_materiel' => 'required|string',
            'categorie_panne' => 'required|string',
            'detail_panne' => 'required|string',
            // Les champs client_id et fournisseur_id doivent être respectivement gérés correctement
            'client_id' => 'required|exists:clients,id',
        ]);
    
        // Récupérer la panne
        $panne = Panne::findOrFail($id);
        
        // Mise à jour des champs de la panne
        $panne->date_commande = $validated['date_commande'] ?? $panne->date_commande;  // Garder la date de commande précédente si elle n'est pas fournie
        $panne->date_panne = $validated['date_panne'];
        $panne->categorie_materiel = $validated['categorie_materiel'];
        $panne->categorie_panne = $validated['categorie_panne'];
        $panne->detail_panne = $validated['detail_panne'];
        
        // Pas besoin de modifier client_id ou fournisseur_id, car ce sont les mêmes
        $panne->save();  // Sauvegarde des changements de la panne
    
        Log::info('Panne mise à jour', ['panne' => $panne]);
    
        // Les relations ne doivent pas être modifiées, donc pas besoin de les synchroniser ou les mettre à jour ici
        // Logiquement, le client et le fournisseur doivent rester les mêmes.
    
        return redirect()->route('panne.index')->with('success', 'Panne mise à jour avec succès');
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $panne = Panne::findOrFail($id);

        // Supprimer les relations avec les clients
        $panne->clients()->detach();

        // Supprimer la panne
        $panne->delete();

        return redirect()->route('panne.index')->with('success', 'Panne supprimée avec succès');
    }
}
