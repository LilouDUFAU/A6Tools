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
                Log::debug("Début de la mise à jour du PCRenouv : " . $id);
        
                // Récupérer le PCRenouv à mettre à jour
                $pcrenouv = PCRenouv::findOrFail($id);
                Log::debug("PCRenouv récupéré : " . $pcrenouv->reference);
        
                // Récupérer la référence et déterminer la référence de base
                $fullReference = $pcrenouv->reference;
                Log::debug("Référence complète : " . $fullReference);
        
                // Déterminer si c'est une location ou un prêt
                if (Str::startsWith($fullReference, 'location-') || Str::startsWith($fullReference, 'prêt-')) {
                    $base = Str::startsWith($fullReference, 'location-')
                        ? Str::after($fullReference, 'location-')
                        : Str::after($fullReference, 'prêt-');
        
                    $originalReference = preg_replace('/-\d+$/', '', $base);
                    Log::debug("Référence d'origine : " . $originalReference);
        
                    $originalPc = PCRenouv::where('reference', $originalReference)->first();
                    if (!$originalPc) {
                        Log::error("Le PC d'origine n'a pas été trouvé avec la référence : " . $originalReference);
                        throw new \Exception("Le PC d'origine n'a pas été trouvé.");
                    }
                    Log::debug("PCRenouv d'origine trouvé : " . $originalPc->reference);
                } else {
                    $originalPc = $pcrenouv; // Le PC modifié est déjà le PC d'origine
                    Log::debug("Le PCRenouv modifié est déjà le PC d'origine.");
                }
        
                // Récupérer les quantités actuelles
                $currentPivot = $pcrenouv->stocks()->where('stock_id', $request->stock_id)->first();
                $currentQuantityInPivot = $currentPivot ? $currentPivot->pivot->quantite : 0;
                Log::debug("Quantité actuelle dans pivot : " . $currentQuantityInPivot);
        
                // Mise à jour du PCRenouv
                $pcrenouv->update([
                    'reference' => $request->reference,
                    'quantite' => $request->quantite,
                    'caracteristiques' => $request->caracteristiques,
                    'type' => $request->type,
                    'statut' => $request->statut,
                ]);
                Log::debug("PCRenouv mis à jour.");
        
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
                Log::debug("Table pivot pcrenouv_stock mise à jour.");
        
                // Calculer et appliquer la différence sur le PC d'origine si nécessaire
                if ($originalPc->id !== $pcrenouv->id) {
                    $difference = $request->quantite - $currentQuantityInPivot;
                    Log::debug("Différence de quantité à appliquer : " . $difference);
        
                    if ($difference !== 0) {
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
                        Log::debug("Quantité du PC d'origine mise à jour : " . $newOriginalQuantity);
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
                                Log::debug("Client mis à jour : " . $client->nom);
        
                                $pcrenouv->clients()->updateExistingPivot($clientId, [
                                    'date_pret' => $clientData['date_pret'] ?? null,
                                    'date_retour' => $clientData['date_retour'] ?? null,
                                    'updated_at' => now(),
                                ]);
                                Log::debug("Dates mises à jour pour client : " . $client->nom);
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
            

    public function destroy(string $id)
    {
        $pcrenouv = PCRenouv::findOrFail($id);
        $pcrenouv->stocks()->detach();
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
                    'date_pret' => now(),
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
                    'date_pret' => now(),
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
}
