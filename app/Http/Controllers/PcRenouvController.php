<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PCRenouv;
use App\Models\User;
use App\Models\Stock;
use App\Models\StockRenouv;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


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

    // Validation des données
    $validated = $request->validate([
        'numero_serie' => 'required|string|max:255',
        'reference' => 'required|string|max:255',
        'quantite' => 'required|integer',
        'caracteristiques' => 'nullable|string|max:5000',
        'type' => 'required|string|max:255',
        'statut' => 'required|string|max:255',
        'stock_id' => 'required|exists:stocks,id',
    ]);

    // Ajouter l'id de l'employé connecté
    $validated['employe_id'] = auth()->id();

    // Si la référence existe déjà dans la base de données, on récupère les PC associés
    $existingPc = PCRenouv::where('reference', $validated['reference'])->get();

    // Si des PC existent déjà avec cette référence, on va mettre à jour leur quantité
    if ($existingPc->isNotEmpty()) {
        $totalQuantity = $existingPc->sum('quantite') + $validated['quantite'];

        // Mettre à jour tous les PC associés à cette référence
        foreach ($existingPc as $pc) {
            $pc->update(['quantite' => $totalQuantity]);
        }
    } else {
        // Si aucune référence n'existe encore, on crée un nouveau PCRenouv
        $pcrenouv = PCRenouv::create($validated);

        // Attacher le stock
        $pcrenouv->stocks()->attach($request->input('stock_id'), ['quantite' => $request->input('quantite')]);
    }

    return redirect()->route('gestrenouv.index')->with('success', 'PCRenouv créé avec succès.');
}


    public function show(string $id)
    {
        $pcrenouv = PCRenouv::with(['employe', 'stocks', 'clients'])->findOrFail($id);
        return view('gestrenouv.show', compact('pcrenouv'));
    }

    public function edit(string $id)
    {
        $pcrenouv = PCRenouv::with('stocks')->findOrFail($id);
        $stocks = Stock::LIEUX;
        $type = PCRenouv::TYPES;
        $statut = PCRenouv::STATUTS;
        $clients = Client::all();

        return view('gestrenouv.edit', compact('pcrenouv', 'stocks', 'type', 'statut', 'clients'));
    }

public function update(Request $request, string $id)
{
    try {
        DB::transaction(function () use ($request, $id) {
    
            // Récupérer le PCRenouv à mettre à jour
            $pcrenouv = PCRenouv::findOrFail($id);
    
            // Récupérer la référence et déterminer la référence de base
            $fullReference = $pcrenouv->reference;
    
            // Déterminer si c'est une location ou un prêt
            if (Str::startsWith($fullReference, 'location-') || Str::startsWith($fullReference, 'prêt-')) {
                $base = Str::startsWith($fullReference, 'location-')
                    ? Str::after($fullReference, 'location-')
                    : Str::after($fullReference, 'prêt-');
    
                $originalReference = preg_replace('/-\d+$/', '', $base);
    
                // Récupérer tous les PC avec la même référence
                $originalPcs = PCRenouv::where('reference', $originalReference)->get();
                if ($originalPcs->isEmpty()) {
                    throw new \Exception("Aucun PC d'origine n'a été trouvé avec cette référence.");
                }
            } else {
                $originalPcs = collect([$pcrenouv]); // Si ce n'est pas une location ou un prêt, considérer le PC actuel comme l'unique
            }
    
            // Récupérer les quantités actuelles
            $currentPivot = $pcrenouv->stocks()->where('stock_id', $request->stock_id)->first();
            $currentQuantityInPivot = $currentPivot ? $currentPivot->pivot->quantite : 0;
    
            // Mise à jour du PCRenouv
            $pcrenouv->update([
                'numero_serie' => $request->numero_serie,
                'reference' => $request->reference,
                'quantite' => $request->quantite,
                'caracteristiques' => $request->caracteristiques,
                'type' => $request->type,
                'statut' => $request->statut,
            ]);
    
            // Mise à jour de la table pivot pcrenouv_stock
            if ($currentPivot) {
                $pcrenouv->stocks()->updateExistingPivot($request->stock_id, [
                    'quantite' => $request->quantite,
                    'updated_at' => now()
                ]);
            } else {
                $pcrenouv->stocks()->attach($request->stock_id, [
                    'quantite' => $request->quantite,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
    
            // Calculer et appliquer la différence sur tous les PC d'origine si nécessaire
            if ($originalPcs->first()->id !== $pcrenouv->id) {
                $difference = $request->quantite - $currentQuantityInPivot;
    
                if ($difference !== 0) {
                    foreach ($originalPcs as $originalPc) {
                        $originalPcPivot = $originalPc->stocks()->where('stock_id', $request->stock_id)->first();
                        if (!$originalPcPivot) {
                            throw new \Exception("Le stock associé au PC d'origine n'a pas été trouvé.");
                        }
    
                        $newOriginalQuantity = $originalPcPivot->pivot->quantite - $difference;
                        if ($newOriginalQuantity < 0) {
                            throw new \Exception("La quantité disponible dans le PC d'origine est insuffisante.");
                        }
    
                        $originalPc->stocks()->updateExistingPivot($request->stock_id, [
                            'quantite' => $newOriginalQuantity,
                            'updated_at' => now()
                        ]);
                        
                        $originalPc->update(['quantite' => $newOriginalQuantity]);
                    }
                }
            }
    
            // Mise à jour des clients (si présents)
            if ($request->has('clients')) {
                foreach ($request->clients as $clientId => $clientData) {
                    if (!empty($clientData['nom']) && !empty($clientData['code_client'])) {
                        $client = Client::find($clientId);
                        if ($client) {
                            $client->update([
                                'nom' => $clientData['nom'],
                                'code_client' => $clientData['code_client'],
                            ]);
    
                            $pcrenouv->clients()->updateExistingPivot($clientId, [
                                'date_pret' => $clientData['date_pret'] ?? null,
                                'date_retour' => $clientData['date_retour'] ?? null,
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }
        });
    
        Log::debug("Mise à jour terminée avec succès.");
        return redirect()->route('gestrenouv.index')
            ->with('success', 'PCRenouv mis à jour avec succès.');
    } catch (\Exception $e) {
        Log::error("Erreur lors de la mise à jour : " . $e->getMessage());
        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'Une erreur est survenue lors de la mise à jour : ' . $e->getMessage()]);
    }
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


    public function preter(string $id)
    {
        $pcrenouv = PCRenouv::findOrFail($id);
        $stocks = Stock::LIEUX;
        $type = PCRenouv::TYPES;
        $statut = PCRenouv::STATUTS;
        $clients = Client::all();

        return view('gestrenouv.preter', compact('pcrenouv', 'stocks', 'type', 'statut', 'clients'));
    }    

    public function addLocPret(Request $request, $id)
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

                if (!empty($validated['new_client']['nom'])) {
                    $client = Client::create([
                        'nom' => $validated['new_client']['nom'],
                        'code_client' => $validated['new_client']['code_client'],
                    ]);
                } else {
                    $client = Client::findOrFail($validated['client_id']);
                }

                $validated['employe_id'] = auth()->id();

                $newPc = Pcrenouv::create([
                    'reference' => $validated['reference'],
                    'quantite' => $validated['quantite'],
                    'caracteristiques' => $validated['caracteristiques'] ?? null,
                    'type' => $validated['type'],
                    'statut' => $validated['statut'],
                    'employe_id' => $validated['employe_id'],
                ]);

                $client->pcrenouv()->attach($newPc->id, [
                    'date_pret' => $validated['date_pret'] ?? now(),
                    'date_retour' => $validated['date_retour'] ?? null,
                ]);

                $stock = Stock::findOrFail($validated['stock_id']);
                $newPc->stocks()->attach($stock->id, [
                    'quantite' => $validated['quantite'],
                ]);

                $pivot = $originalPc->stocks()->where('stock_id', $stock->id)->first();

                if ($pivot) {
                    $currentQty = $pivot->pivot->quantite;

                    if ($currentQty >= $validated['quantite']) {
                        $originalPc->stocks()->updateExistingPivot($stock->id, [
                            'quantite' => $currentQty - $validated['quantite'],
                        ]);

                        $originalPc->update([
                            'quantite' => $originalPc->quantite - $validated['quantite'],
                        ]);
                    } else {
                        throw new \Exception("Quantité insuffisante dans le stock pour le PC sélectionné.");
                    }
                } else {
                    throw new \Exception("Le PC d'origine n'est pas associé à ce stock.");
                }
            });

            return redirect()->route('gestrenouv.index')->with('success', 'Location enregistrée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function retour($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $pcrenouvLocation = Pcrenouv::findOrFail($id);

                $fullReference = $pcrenouvLocation->reference;

                if (Str::startsWith($fullReference, 'location-')) {
                    $base = Str::after($fullReference, 'location-');
                } elseif (Str::startsWith($fullReference, 'prêt-')) {
                    $base = Str::after($fullReference, 'prêt-');
                } else {
                    $base = $fullReference;
                }

                $originalReference = preg_replace('/-\d+$/', '', $base);

                $originalPc = Pcrenouv::where('reference', $originalReference)->first();
                if (!$originalPc) {
                    throw new \Exception("Le PC d'origine n'a pas été trouvé.");
                }

                if ($pcrenouvLocation->statut == 'loué' || $pcrenouvLocation->statut == 'prêté') {

                    $stock = $pcrenouvLocation->stocks()->first();
                    if (!$stock) {
                        throw new \Exception("Le stock associé à cette location n'a pas été trouvé.");
                    }

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
                    } else {
                        throw new \Exception("Le stock associé au PC d'origine n'a pas été trouvé dans la relation pivot.");
                    }

                    $pcrenouvLocation->delete();

                } else {
                    throw new \Exception("L'état de la location n'est ni 'loué' ni 'prêté'.");
                }
            });

            return redirect()->route('gestrenouv.index')->with('success', 'Retour enregistré avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $pcrenouv = PCRenouv::with('stocks', 'clients')->findOrFail($id);
                $reference = $pcrenouv->reference;
    
                if (Str::startsWith($reference, 'location-') || Str::startsWith($reference, 'prêt-')) {
                    // 🔁 C'est une location ou un prêt
                    $base = Str::startsWith($reference, 'location-')
                        ? Str::after($reference, 'location-')
                        : Str::after($reference, 'prêt-');
                    $originalReference = preg_replace('/-\d+$/', '', $base);
    
                    $originalPc = PCRenouv::where('reference', $originalReference)->firstOrFail();
    
                    $stock = $pcrenouv->stocks()->first();
                    if (!$stock) {
                        throw new \Exception("Stock introuvable pour la location/prêt.");
                    }
    
                    $pivot = $originalPc->stocks()->where('stock_id', $stock->id)->first();
                    if (!$pivot) {
                        throw new \Exception("Le stock associé au PC d'origine n'a pas été trouvé dans la relation pivot.");
                    }
    
                    // 🧮 Mise à jour des quantités
                    $newQty = $pivot->pivot->quantite + $pcrenouv->quantite;
    
                    $originalPc->stocks()->updateExistingPivot($stock->id, [
                        'quantite' => $newQty,
                        'updated_at' => now(),
                    ]);
    
                    $originalPc->update(['quantite' => $originalPc->quantite + $pcrenouv->quantite]);
    
                    // 🧽 Nettoyage de la relation client_pcrenouv
                    $pcrenouv->clients()->detach();
                } else {
                    // 🧹 C'est un PC d'origine → on supprime toutes les locations/prêts qui en dérivent
                    $locationsEtPrets = PCRenouv::where(function ($query) use ($reference) {
                        $query->where('reference', 'like', 'location-' . $reference . '-%')
                            ->orWhere('reference', 'like', 'prêt-' . $reference . '-%');
                    })->get();
    
                    foreach ($locationsEtPrets as $pc) {
                        $pc->stocks()->detach();
                        $pc->clients()->detach(); // 🧽 Détachement client-location/pret
                        $pc->delete();
                    }
                }
    
                // 🔚 Suppression finale du PC
                $pcrenouv->stocks()->detach();
                $pcrenouv->clients()->detach(); // toujours détacher les relations clients avant suppression
                $pcrenouv->delete();
            });
    
            return redirect()->route('gestrenouv.index')->with('success', 'PCRenouv supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }
    


       
}
