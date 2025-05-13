<?php

namespace App\Http\Controllers;

use App\Models\PCRenouv;
use App\Models\Stock;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PCRenouvController extends Controller
{
    public function index()
    {
        $pcrenouvs = PCRenouv::with(['stocks', 'clients'])->get();
        return view('gestrenouv.index', compact('pcrenouvs'));
    }

    public function create()
    {
        $type = PCRenouv::TYPES;
        $statut = PCRenouv::STATUTS;
        return view('gestrenouv.create', compact('type', 'statut'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:255',
            'numero_serie' => 'required|string|max:255|unique:p_c_renouvs,numero_serie',
            'caracteristiques' => 'nullable|string|max:255',
            'type' => 'required|string|in:' . implode(',', PCRenouv::TYPES),
            'statut' => 'required|string|in:' . implode(',', PCRenouv::STATUTS),
            'stock_id' => 'required|exists:stocks,id',
            'quantite' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Always create a new PCRenouv entry
            $pcrenouv = PCRenouv::create([
                'reference' => $validated['reference'],
                'numero_serie' => $validated['numero_serie'],
                'caracteristiques' => $validated['caracteristiques'],
                'type' => $validated['type'],
                'statut' => $validated['statut'],
                'employe_id' => auth()->id(),
            ]);

            // Attach to stock with the specified quantity
            $pcrenouv->stocks()->attach($validated['stock_id'], ['quantite' => $validated['quantite']]);
            
            DB::commit();
            return redirect()->route('gestrenouv.index')->with('success', 'PCRenouv créé avec succès!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Erreur lors de la création du PCRenouv: ' . $e->getMessage())->withInput();
        }
    }

    public function show(string $id)
    {
        $pcrenouv = PCRenouv::with(['stocks', 'clients'])->findOrFail($id);
        return view('gestrenouv.show', compact('pcrenouv'));
    }

    public function edit(string $id)
    {
        $pcrenouv = PCRenouv::with(['stocks', 'clients'])->findOrFail($id);
        $type = PCRenouv::TYPES;
        $statut = PCRenouv::STATUTS;
        return view('gestrenouv.edit', compact('pcrenouv', 'type', 'statut'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:255',
            'numero_serie' => 'required|string|max:255',
            'caracteristiques' => 'nullable|string|max:255',
            'type' => 'required|string|in:' . implode(',', PCRenouv::TYPES),
            'statut' => 'required|string|in:' . implode(',', PCRenouv::STATUTS),
            'stock_id' => 'required|exists:stocks,id',
            'clients' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $pcrenouv = PCRenouv::findOrFail($id);
            
            // Update PCRenouv
            $pcrenouv->update([
                'reference' => $validated['reference'],
                'numero_serie' => $validated['numero_serie'],
                'caracteristiques' => $validated['caracteristiques'],
                'type' => $validated['type'],
                'statut' => $validated['statut'],
            ]);

            // Update stock association
            if ($pcrenouv->stocks->isEmpty()) {
                $pcrenouv->stocks()->attach($validated['stock_id'], ['quantite' => 1]);
            } else {
                $pcrenouv->stocks()->updateExistingPivot($pcrenouv->stocks->first()->id, [
                    'stock_id' => $validated['stock_id'],
                ]);
            }

            // Process client updates if available
            if (!empty($validated['clients'])) {
                foreach ($validated['clients'] as $clientId => $clientData) {
                    $pcrenouv->clients()->updateExistingPivot($clientId, [
                        'date_pret' => $clientData['date_pret'] ?? null,
                        'date_retour' => $clientData['date_retour'] ?? null,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('gestrenouv.index')->with('success', 'PCRenouv mis à jour avec succès!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour du PCRenouv: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $pcrenouv = PCRenouv::findOrFail($id);
            $pcrenouv->delete();
            return redirect()->route('gestrenouv.index')->with('success', 'PCRenouv supprimé avec succès!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression du PCRenouv: ' . $e->getMessage());
        }
    }

    public function louer($id)
    {
        // Check if this is a group loan
        $isGroup = request()->has('isGroup') && request()->input('isGroup') === 'true';
        $reference = request()->input('reference');
        
        if ($isGroup && $reference) {
            // Find all PCRenouvs with the same reference and 'en stock' status
            $pcrenouvs = PCRenouv::where('reference', $reference)
                              ->where('statut', 'en stock')
                              ->with('stocks')
                              ->get();
            
            $totalQuantity = $pcrenouvs->sum(function($r) {
                return $r->stocks->first()?->pivot->quantite ?? 0;
            });
            
            // Use first PCRenouv for form defaults
            $pcrenouv = $pcrenouvs->first();
            $pcrenouv->isGroup = true;
            $pcrenouv->totalQuantity = $totalQuantity;
            $pcrenouv->groupItems = $pcrenouvs;
        } else {
            $pcrenouv = PCRenouv::with('stocks')->findOrFail($id);
        }
        
        $type = PCRenouv::TYPES;
        $statut = PCRenouv::STATUTS;
        $clients = Client::all();
        
        return view('gestrenouv.louer', compact('pcrenouv', 'type', 'statut', 'clients'));
    }

    public function preter($id)
    {
        // Check if this is a group loan
        $isGroup = request()->has('isGroup') && request()->input('isGroup') === 'true';
        $reference = request()->input('reference');
        
        if ($isGroup && $reference) {
            // Find all PCRenouvs with the same reference and 'en stock' status
            $pcrenouvs = PCRenouv::where('reference', $reference)
                              ->where('statut', 'en stock')
                              ->with('stocks')
                              ->get();
            
            $totalQuantity = $pcrenouvs->sum(function($r) {
                return $r->stocks->first()?->pivot->quantite ?? 0;
            });
            
            // Use first PCRenouv for form defaults
            $pcrenouv = $pcrenouvs->first();
            $pcrenouv->isGroup = true;
            $pcrenouv->totalQuantity = $totalQuantity;
            $pcrenouv->groupItems = $pcrenouvs;
        } else {
            $pcrenouv = PCRenouv::with('stocks')->findOrFail($id);
        }
        
        $type = PCRenouv::TYPES;
        $statut = PCRenouv::STATUTS;
        $clients = Client::all();
        
        return view('gestrenouv.preter', compact('pcrenouv', 'type', 'statut', 'clients'));
    }

    public function addLocPret(Request $request, $id)
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:255',
            'numero_serie' => 'required|string|max:255',
            'caracteristiques' => 'nullable|string|max:255',
            'type' => 'required|string|in:' . implode(',', PCRenouv::TYPES),
            'statut' => 'required|string|in:' . implode(',', PCRenouv::STATUTS),
            'stock_id' => 'required|exists:stocks,id',
            'quantite' => 'required|integer|min:1',
            'client_id' => 'nullable|exists:clients,id',
            'new_client' => 'nullable|array',
            'date_pret' => 'nullable|date',
            'date_retour' => 'nullable|date',
            'is_group' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Determine if this is a group operation
            $isGroup = $request->has('is_group') && $request->input('is_group');
            
            if ($isGroup) {
                // Get the reference without prefix to find the original items
                $originalReference = preg_replace('/^(prêt-|location-)/', '', $validated['reference']);
                $originalReference = preg_replace('/-\d+$/', '', $originalReference);
                
                $pcrenouvs = PCRenouv::where('reference', $originalReference)
                                  ->where('statut', 'en stock')
                                  ->with('stocks')
                                  ->get();
                
                $totalAvailableQuantity = $pcrenouvs->sum(function($r) {
                    return $r->stocks->first()?->pivot->quantite ?? 0;
                });
                
                // Validation: Check if requested quantity is available
                if ($validated['quantite'] > $totalAvailableQuantity) {
                    return redirect()->back()->with('error', 'Quantité demandée non disponible. Disponible: ' . $totalAvailableQuantity)->withInput();
                }
                
                // Create a new PCRenouv record for loan/lend
                $loanedPcrenouv = PCRenouv::create([
                    'reference' => $validated['reference'],
                    'numero_serie' => $validated['numero_serie'],
                    'caracteristiques' => $validated['caracteristiques'],
                    'type' => $validated['type'],
                    'statut' => $validated['statut'],
                    'employe_id' => auth()->id(),
                ]);
                
                // Attach to stock with requested quantity
                $loanedPcrenouv->stocks()->attach($validated['stock_id'], ['quantite' => $validated['quantite']]);
                
                // Reduce quantities from original PCRenouvs
                $remainingToReduce = $validated['quantite'];
                
                foreach ($pcrenouvs as $pcrenouv) {
                    $currentQty = $pcrenouv->stocks->first()?->pivot->quantite ?? 0;
                    
                    if ($currentQty <= 0) continue;
                    
                    $toReduce = min($remainingToReduce, $currentQty);
                    $newQty = $currentQty - $toReduce;
                    
                    if ($newQty <= 0) {
                        // If no more items, detach from stock and delete the record
                        $pcrenouv->stocks()->detach();
                        $pcrenouv->delete();
                    } else {
                        // Update the quantity
                        $pcrenouv->stocks()->updateExistingPivot(
                            $pcrenouv->stocks->first()->id,
                            ['quantite' => $newQty]
                        );
                    }
                    
                    $remainingToReduce -= $toReduce;
                    if ($remainingToReduce <= 0) break;
                }
            } else {
                // Single item process - original logic
                $originalPcrenouv = PCRenouv::with('stocks')->findOrFail($id);
                
                // Get total quantity for this item
                $totalQuantity = $originalPcrenouv->stocks->first()?->pivot->quantite ?? 0;
                
                // Validation: Check if requested quantity is available
                if ($validated['quantite'] > $totalQuantity) {
                    return redirect()->back()->with('error', 'Quantité demandée non disponible. Disponible: ' . $totalQuantity)->withInput();
                }
                
                // Create a new PCRenouv record for loan/lend
                $loanedPcrenouv = PCRenouv::create([
                    'reference' => $validated['reference'],
                    'numero_serie' => $validated['numero_serie'],
                    'caracteristiques' => $validated['caracteristiques'],
                    'type' => $validated['type'],
                    'statut' => $validated['statut'],
                    'employe_id' => auth()->id(),
                ]);
                
                // Attach to stock with requested quantity
                $loanedPcrenouv->stocks()->attach($validated['stock_id'], ['quantite' => $validated['quantite']]);
                
                // Reduce the quantity from original PCRenouv
                $remainingQuantity = $totalQuantity - $validated['quantite'];
                
                if ($remainingQuantity <= 0) {
                    // If no more items, detach from stock and delete the record
                    $originalPcrenouv->stocks()->detach();
                    $originalPcrenouv->delete();
                } else {
                    // Update the quantity
                    $originalPcrenouv->stocks()->updateExistingPivot(
                        $originalPcrenouv->stocks->first()->id,
                        ['quantite' => $remainingQuantity]
                    );
                }
            }
            
            // Process client
            $clientId = null;
            
            // Check if we're creating a new client
            if (isset($validated['new_client']) && !empty($validated['new_client']['nom'])) {
                $client = Client::create([
                    'nom' => $validated['new_client']['nom'],
                    'code_client' => $validated['new_client']['code_client'] ?? null,
                ]);
                $clientId = $client->id;
            } elseif (!empty($validated['client_id'])) {
                $clientId = $validated['client_id'];
            }
            
            // Attach client to the PCRenouv
            if ($clientId) {
                $loanedPcrenouv->clients()->attach($clientId, [
                    'date_pret' => $validated['date_pret'] ?? now(),
                    'date_retour' => $validated['date_retour'],
                ]);
            }
            
            DB::commit();
            return redirect()->route('gestrenouv.index')->with('success', 'PCRenouv ' . $validated['statut'] . ' avec succès!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage())->withInput();
        }
    }

    public function retour(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $pcrenouv = PCRenouv::with(['stocks', 'clients'])->findOrFail($id);
            
            // Validate the PC is loaned or lent
            if (!in_array(strtolower($pcrenouv->statut), ['loué', 'prêté'])) {
                return redirect()->back()->with('error', 'Ce PCRenouv n\'est pas prêté ou loué.');
            }
            
            // Get the reference to find the original item
            $reference = $pcrenouv->reference;
            // Extract base reference (remove 'prêt-' or 'location-' prefix)
            $baseReference = preg_replace('/^(prêt-|location-)/', '', $reference);
            $baseReference = preg_replace('/-\d+$/', '', $baseReference); // Remove timestamp suffix
            
            // Find original PCRenouv with same base reference
            $originalPCRenouv = PCRenouv::where('reference', $baseReference)
                                        ->where('statut', 'en stock')
                                        ->with('stocks')
                                        ->first();
            
            $returnQuantity = $pcrenouv->stocks->first()->pivot->quantite ?? 0;
            
            if ($originalPCRenouv) {
                // Original exists, update its quantity
                $originalStock = $originalPCRenouv->stocks->first();
                if ($originalStock) {
                    $currentQuantity = $originalStock->pivot->quantite ?? 0;
                    $originalPCRenouv->stocks()->updateExistingPivot(
                        $originalStock->id,
                        ['quantite' => $currentQuantity + $returnQuantity]
                    );
                } else {
                    // Original has no stock, attach new
                    $originalPCRenouv->stocks()->attach($pcrenouv->stocks->first()->id, [
                        'quantite' => $returnQuantity
                    ]);
                }
            } else {
                // Create a new 'en stock' record
                $newPCRenouv = PCRenouv::create([
                    'reference' => $baseReference,
                    'numero_serie' => preg_replace('/^(prêt-|location-)/', '', $pcrenouv->numero_serie),
                    'caracteristiques' => $pcrenouv->caracteristiques,
                    'type' => $pcrenouv->type,
                    'statut' => 'en stock',
                    'employe_id' => auth()->id(),
                ]);
                
                // Attach to stock
                $newPCRenouv->stocks()->attach($pcrenouv->stocks->first()->id, [
                    'quantite' => $returnQuantity
                ]);
            }
            
            // Delete the loan/lend record
            $pcrenouv->delete();
            
            DB::commit();
            return redirect()->route('gestrenouv.index')->with('success', 'PCRenouv retourné avec succès!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Erreur lors du retour: ' . $e->getMessage());
        }
    }
}