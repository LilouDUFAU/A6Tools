<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PCRenouv;
use App\Models\User;
use App\Models\Stock;
use App\Models\StockRenouv;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PcRenouvController extends Controller
{
    public function index()
    {
        $pcrenouvs = PCRenouv::with(['employe', 'stocks'])->get();
        return view('gestrenouv.index', compact('pcrenouvs'));
    }

    public function create()
    {
        $stocks = Stock::LIEUX;
        $type = PCRenouv::TYPES;
        $statut = PCRenouv::STATUTS;

        return view('gestrenouv.create', compact('stocks', 'type', 'statut'));
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour créer un PCRenouv.');
        }

        $validated = $request->validate([
            'reference' => 'required|string|max:255',
            'quantite' => 'required|integer',
            'caracteristiques' => 'nullable|string|max:5000',
            'type' => 'required|string|max:255',
            'statut' => 'required|string|max:255',
            'stock_id' => 'required|exists:stocks,id',
        ]);

        $validated['employe_id'] = auth()->id();

        $pcrenouv = PCRenouv::create($validated);
        $pcrenouv->stocks()->attach($request->input('stock_id'), ['quantite' => $request->input('quantite')]);

        return redirect()->route('gestrenouv.index')->with('success', 'PCRenouv créé avec succès.');
    }

    public function show(string $id)
    {
        $pcrenouv = PCRenouv::with(['employe', 'stocks'])->findOrFail($id);
        return view('gestrenouv.show', compact('pcrenouv'));
    }

    public function edit(string $id)
    {
        $pcrenouv = PCRenouv::with('stocks')->findOrFail($id);
        $stocks = Stock::LIEUX;
        $type = PCRenouv::TYPES;
        $statut = PCRenouv::STATUTS;

        return view('gestrenouv.edit', compact('pcrenouv', 'stocks', 'type', 'statut'));
    }

    public function update(Request $request, string $id)
    {
        $pcrenouv = PCRenouv::findOrFail($id);

        $validated = $request->validate([
            'reference' => 'required|string|max:255',
            'quantite' => 'required|integer',
            'caracteristiques' => 'nullable|string|max:255',
            'type' => 'required|string|max:255',
            'statut' => 'required|string|max:255',
            'stock_id' => 'required|exists:stocks,id',
        ]);

        $pcrenouv->update($validated);

        // Mettre à jour la relation pivot stock_quantité
        $pcrenouv->stocks()->sync([
            $request->input('stock_id') => ['quantite' => $request->input('quantite')]
        ]);

        return redirect()->route('gestrenouv.index')->with('success', 'PCRenouv mis à jour avec succès.');
    }

    public function destroy(string $id)
    {
        $pcrenouv = PCRenouv::findOrFail($id);

        // Détacher les relations avec les stocks
        $pcrenouv->stocks()->detach();

        // Supprimer l'enregistrement
        $pcrenouv->delete();

        return redirect()->route('gestrenouv.index')->with('success', 'PCRenouv supprimé avec succès.');
    }

    public function louer(string $id)
    {
        $pcrenouv = PCRenouv::findOrFail($id);
        $stocks = Stock::LIEUX;
        $type = PCRenouv::TYPES;
        $statut = PCRenouv::STATUTS;
        $clients = Client::all();

        return view('gestrenouv.louer', compact('pcrenouv', 'stocks', 'type', 'statut', 'clients'));
    }

    public function addLoc(Request $request, $id)
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:255',
            'quantite' => 'required|integer|min:1',
            'caracteristiques' => 'nullable|string',
            'type' => 'required|string',
            'statut' => 'required|string',
            'stock_id' => 'required|exists:stocks,id',
            'client_id' => 'nullable|exists:clients,id',
            'new_client.nom' => 'nullable|string|max:255',
            'new_client.code_client' => 'nullable|string|max:255',
            'date_retour' => 'nullable|date',
        ]);

        try {
            DB::transaction(function () use ($validated, $id) {
                $originalPc = Pcrenouv::findOrFail($id);
                Log::info("Original PC trouvé: ", ['id' => $originalPc->id, 'quantite' => $originalPc->quantite]);

                // Création ou récupération du client
                if (!empty($validated['new_client']['nom'])) {
                    $client = Client::create([
                        'nom' => $validated['new_client']['nom'],
                        'code_client' => $validated['new_client']['code_client'],
                    ]);
                } else {
                    $client = Client::findOrFail($validated['client_id']);
                }

                // Ajout de l'employé connecté
                $validated['employe_id'] = auth()->id();

                // Création du nouveau PC
                $newPc = Pcrenouv::create([
                    'reference' => $validated['reference'],
                    'quantite' => $validated['quantite'],
                    'caracteristiques' => $validated['caracteristiques'] ?? null,
                    'type' => $validated['type'],
                    'statut' => $validated['statut'],
                    'employe_id' => $validated['employe_id'],
                ]);
                Log::info("Nouveau PC créé: ", ['id' => $newPc->id, 'quantite' => $newPc->quantite]);

                // Lier le client
                $client->pcrenouv()->attach($newPc->id, [
                    'date_pret' => now(),
                    'date_retour' => $validated['date_retour'] ?? null,
                ]);

                // Lier le nouveau PC au stock avec la quantité
                $stock = Stock::findOrFail($validated['stock_id']);
                $newPc->stocks()->attach($stock->id, [
                    'quantite' => $validated['quantite'],
                ]);

                // Mise à jour de la quantité sur la table pivot pour le PC d'origine
                $pivot = $originalPc->stocks()->where('stock_id', $stock->id)->first();

                if ($pivot) {
                    $currentQty = $pivot->pivot->quantite;
                    Log::info("Quantité actuelle dans le stock: ", ['quantite' => $currentQty]);

                    if ($currentQty >= $validated['quantite']) {
                        // Mise à jour de la quantité dans la table pivot
                        $originalPc->stocks()->updateExistingPivot($stock->id, [
                            'quantite' => $currentQty - $validated['quantite'],
                        ]);

                        // Mise à jour de la quantité dans la table p_c_renouv (PC d'origine)
                        $originalPc->update([
                            'quantite' => $originalPc->quantite - $validated['quantite'],
                        ]);

                        Log::info("Quantité mise à jour dans le PC d'origine: ", ['quantite' => $originalPc->quantite]);
                    } else {
                        throw new \Exception("Quantité insuffisante dans le stock pour le PC sélectionné.");
                    }
                } else {
                    throw new \Exception("Le PC d'origine n'est pas associé à ce stock.");
                }
            });

            return redirect()->route('gestrenouv.index')->with('success', 'Location enregistrée avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la location", ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }


    public function preter(string $id)
    {
        $pcrenouv = PCRenouv::findOrFail($id);
        $stocks = Stock::LIEUX;
        $type = PCRenouv::TYPES;
        $statut = PCRenouv::STATUTS;
        $clients = Client::all();

        return view('gestrenouv.preter', compact('pcrenouv', 'stocks', 'type', 'statut', 'clients'));
    }    

    public function addPret(Request $request, $id)
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:255',
            'quantite' => 'required|integer|min:1',
            'caracteristiques' => 'nullable|string',
            'type' => 'required|string',
            'statut' => 'required|string',
            'stock_id' => 'required|exists:stocks,id',
            'client_id' => 'nullable|exists:clients,id',
            'new_client.nom' => 'nullable|string|max:255',
            'new_client.code_client' => 'nullable|string|max:255',
            'date_retour' => 'nullable|date',
        ]);

        try {
            DB::transaction(function () use ($validated, $id) {
                $originalPc = Pcrenouv::findOrFail($id);
                Log::info("Original PC trouvé: ", ['id' => $originalPc->id, 'quantite' => $originalPc->quantite]);

                // Création ou récupération du client
                if (!empty($validated['new_client']['nom'])) {
                    $client = Client::create([
                        'nom' => $validated['new_client']['nom'],
                        'code_client' => $validated['new_client']['code_client'],
                    ]);
                } else {
                    $client = Client::findOrFail($validated['client_id']);
                }

                // Ajout de l'employé connecté
                $validated['employe_id'] = auth()->id();

                // Création du nouveau PC
                $newPc = Pcrenouv::create([
                    'reference' => $validated['reference'],
                    'quantite' => $validated['quantite'],
                    'caracteristiques' => $validated['caracteristiques'] ?? null,
                    'type' => $validated['type'],
                    'statut' => $validated['statut'],
                    'employe_id' => $validated['employe_id'],
                ]);
                Log::info("Nouveau PC créé: ", ['id' => $newPc->id, 'quantite' => $newPc->quantite]);

                // Lier le client
                $client->pcrenouv()->attach($newPc->id, [
                    'date_pret' => now(),
                    'date_retour' => $validated['date_retour'] ?? null,
                ]);

                // Lier le nouveau PC au stock avec la quantité
                $stock = Stock::findOrFail($validated['stock_id']);
                $newPc->stocks()->attach($stock->id, [
                    'quantite' => $validated['quantite'],
                ]);

                // Mise à jour de la quantité sur la table pivot pour le PC d'origine
                $pivot = $originalPc->stocks()->where('stock_id', $stock->id)->first();

                if ($pivot) {
                    $currentQty = $pivot->pivot->quantite;
                    Log::info("Quantité actuelle dans le stock: ", ['quantite' => $currentQty]);

                    if ($currentQty >= $validated['quantite']) {
                        // Mise à jour de la quantité dans la table pivot
                        $originalPc->stocks()->updateExistingPivot($stock->id, [
                            'quantite' => $currentQty - $validated['quantite'],
                        ]);

                        // Mise à jour de la quantité dans la table p_c_renouv (PC d'origine)
                        $originalPc->update([
                            'quantite' => $originalPc->quantite - $validated['quantite'],
                        ]);

                        Log::info("Quantité mise à jour dans le PC d'origine: ", ['quantite' => $originalPc->quantite]);
                    } else {
                        throw new \Exception("Quantité insuffisante dans le stock pour le PC sélectionné.");
                    }
                } else {
                    throw new \Exception("Le PC d'origine n'est pas associé à ce stock.");
                }
            });

            return redirect()->route('gestrenouv.index')->with('success', 'Location enregistrée avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la location", ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function retour($id)
    {
        try {
            DB::transaction(function () use ($id) {
                Log::info("Démarrage de la transaction pour le retour de la location", ['location_id' => $id]);
    
                // Récupérer le PC de la location
                $pcrenouvLocation = Pcrenouv::findOrFail($id);
                Log::info("PC de la location trouvé", [
                    'id' => $pcrenouvLocation->id,
                    'quantite' => $pcrenouvLocation->quantite,
                    'statut' => $pcrenouvLocation->statut,
                    'reference' => $pcrenouvLocation->reference
                ]);
    
                // Extraire la référence du PC d'origine
                $fullReference = $pcrenouvLocation->reference;
    
                if (Str::startsWith($fullReference, 'location-')) {
                    $base = Str::after($fullReference, 'location-');
                } elseif (Str::startsWith($fullReference, 'prêt-')) {
                    $base = Str::after($fullReference, 'prêt-');
                } else {
                    $base = $fullReference;
                }
    
                $originalReference = preg_replace('/-\d+$/', '', $base);
    
                Log::info("Référence du PC d'origine obtenue", ['original_reference' => $originalReference]);
    
                // Trouver le PC d'origine via la référence
                $originalPc = Pcrenouv::where('reference', $originalReference)->first();
                if (!$originalPc) {
                    Log::error("PC d'origine non trouvé avec la référence", ['reference' => $originalReference]);
                    throw new \Exception("Le PC d'origine n'a pas été trouvé.");
                }
    
                Log::info("PC d'origine trouvé", ['original_pc_id' => $originalPc->id, 'quantite' => $originalPc->quantite]);
    
                // Vérifier que l'état est "loué" ou "prêté"
                if ($pcrenouvLocation->statut == 'loué' || $pcrenouvLocation->statut == 'prêté') {
                    Log::info("L'état de la location est valide", ['statut' => $pcrenouvLocation->statut]);
    
                    // Récupérer le stock associé
                    $stock = $pcrenouvLocation->stocks()->first();
                    if (!$stock) {
                        Log::error("Aucun stock trouvé pour cette location", ['location_id' => $id]);
                        throw new \Exception("Le stock associé à cette location n'a pas été trouvé.");
                    }
    
                    Log::info("Stock associé trouvé", ['stock_id' => $stock->id]);
    
                    // Mettre à jour la quantité du stock pour le PC d'origine
                    $pivot = $originalPc->stocks()->where('stock_id', $stock->id)->first();
                    if ($pivot) {
                        $currentQty = $pivot->pivot->quantite;
                        $newQty = $currentQty + $pcrenouvLocation->quantite;
    
                        $originalPc->stocks()->updateExistingPivot($stock->id, [
                            'quantite' => $newQty,
                        ]);
    
                        $originalPc->update([
                            'quantite' => $originalPc->quantite + $pcrenouvLocation->quantite,
                        ]);
    
                        Log::info("Quantité mise à jour dans le stock et le PC d'origine", [
                            'nouvelle_quantite' => $newQty
                        ]);
                    } else {
                        Log::error("Le stock associé au PC d'origine n'a pas été trouvé dans la relation pivot", [
                            'original_pc_id' => $originalPc->id,
                            'stock_id' => $stock->id
                        ]);
                        throw new \Exception("Le stock associé au PC d'origine n'a pas été trouvé dans la relation pivot.");
                    }
    
                    // Supprimer la location ou le prêt
                    $pcrenouvLocation->delete();
                    Log::info("Location ou prêt supprimé avec succès", ['id' => $pcrenouvLocation->id]);
    
                } else {
                    Log::error("L'état de la location est invalide", ['statut' => $pcrenouvLocation->statut]);
                    throw new \Exception("L'état de la location n'est ni 'loué' ni 'prêté'.");
                }
    
            });
    
            Log::info("Retour enregistré avec succès.");
            return redirect()->route('gestrenouv.index')->with('success', 'Retour enregistré avec succès.');
    
        } catch (\Exception $e) {
            Log::error("Erreur lors du retour de la location", [
                'error' => $e->getMessage(),
                'location_id' => $id
            ]);
            return redirect()->back()->with('error', $e->getMessage());
        }
    } 

}
