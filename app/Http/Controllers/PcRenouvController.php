<?php

namespace App\Http\Controllers;

use App\Models\PCRenouv;
use App\Models\Stock;
use App\Models\Client;
use App\Models\LocPret;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PCRenouvController extends Controller
{
    public function index()
    {
        $pcrenouvs = PCRenouv::with(['stocks', 'clients', 'locPret'])->get();
        $userStock = Auth::user()->stock ? Auth::user()->stock->lieux : null;
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
            Log::info('Creating new PCRenouv', $validated);

            $pcrenouv = PCRenouv::create([
                'reference' => $validated['reference'],
                'numero_serie' => $validated['numero_serie'],
                'caracteristiques' => $validated['caracteristiques'],
                'type' => $validated['type'],
                'statut' => $validated['statut'],
                'employe_id' => auth()->id(),
                'quantite' => $validated['quantite'],
                'locPret_id' => null,
            ]);

            Log::info('PCRenouv created successfully', ['id' => $pcrenouv->id]);

            $pcrenouv->stocks()->attach($validated['stock_id'], ['quantite' => $validated['quantite']]);
            
            Log::info('Stock attached to PCRenouv', [
                'pcrenouv_id' => $pcrenouv->id,
                'stock_id' => $validated['stock_id'],
                'quantite' => $validated['quantite']
            ]);

            DB::commit();
            return redirect()->route('gestrenouv.index')->with('success', 'PCRenouv créé avec succès!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating PCRenouv', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            return redirect()->back()->with('error', 'Erreur lors de la création du PCRenouv: ' . $e->getMessage())->withInput();
        }
    }

    public function show(string $id)
    {
        try {
            Log::info('Fetching PCRenouv details', ['id' => $id]);
            
            $pcrenouv = PCRenouv::with(['stocks', 'clients', 'locPret'])->findOrFail($id);
            
            Log::info('PCRenouv details fetched successfully', [
                'id' => $id,
                'reference' => $pcrenouv->reference
            ]);
            
            return view('gestrenouv.show', compact('pcrenouv'));
        } catch (\Exception $e) {
            Log::error('Error fetching PCRenouv details', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Erreur lors de la récupération des détails du PCRenouv: ' . $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        try {
            Log::info('Fetching PCRenouv for editing', ['id' => $id]);
            
            $pcrenouv = PCRenouv::with(['stocks', 'clients', 'locPret'])->findOrFail($id);
            Log::info('PCRenouv fetched successfully', ['id' => $id, 'reference' => $pcrenouv->reference]);
            
            $type = PCRenouv::TYPES;
            $statut = PCRenouv::STATUTS;
            
            return view('gestrenouv.edit', compact('pcrenouv', 'type', 'statut'));
        } catch (\Exception $e) {
            Log::error('Error fetching PCRenouv for editing', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Erreur lors de la récupération du PCRenouv: ' . $e->getMessage());
        }
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
            Log::info('Updating PCRenouv', ['id' => $id, 'data' => $validated]);

            $pcrenouv = PCRenouv::findOrFail($id);
            
            $pcrenouv->update([
                'reference' => $validated['reference'],
                'numero_serie' => $validated['numero_serie'],
                'caracteristiques' => $validated['caracteristiques'],
                'type' => $validated['type'],
                'statut' => $validated['statut'],
            ]);

            Log::info('PCRenouv updated successfully', ['id' => $pcrenouv->id]);

            if ($pcrenouv->stocks->isEmpty()) {
                $pcrenouv->stocks()->attach($validated['stock_id'], ['quantite' => 1]);
                Log::info('New stock attached to PCRenouv', [
                    'pcrenouv_id' => $pcrenouv->id,
                    'stock_id' => $validated['stock_id']
                ]);
            } else {
                $pcrenouv->stocks()->updateExistingPivot($pcrenouv->stocks->first()->id, [
                    'stock_id' => $validated['stock_id'],
                ]);
                Log::info('Stock updated for PCRenouv', [
                    'pcrenouv_id' => $pcrenouv->id,
                    'stock_id' => $validated['stock_id']
                ]);
            }

            if (!empty($validated['clients'])) {
                foreach ($validated['clients'] as $clientId => $clientData) {
                    $pcrenouv->clients()->updateExistingPivot($clientId, [
                        'date_pret' => $clientData['date_pret'] ?? null,
                        'date_retour' => $clientData['date_retour'] ?? null,
                    ]);
                    Log::info('Client association updated', [
                        'pcrenouv_id' => $pcrenouv->id,
                        'client_id' => $clientId,
                        'data' => $clientData
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('gestrenouv.index')->with('success', 'PCRenouv mis à jour avec succès!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating PCRenouv', [
                'id' => $id,
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour du PCRenouv: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            Log::info('Deleting PCRenouv', ['id' => $id]);
            $pcrenouv = PCRenouv::findOrFail($id);
            $pcrenouv->delete();
            Log::info('PCRenouv deleted successfully', ['id' => $id]);
            return redirect()->route('gestrenouv.index')->with('success', 'PCRenouv supprimé avec succès!');
        } catch (\Exception $e) {
            Log::error('Error deleting PCRenouv', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Erreur lors de la suppression du PCRenouv: ' . $e->getMessage());
        }
    }

    public function louer($id)
    {
        try {
            Log::info('Starting loan process', ['id' => $id]);
            
            $isGroup = request()->has('isGroup') && request()->input('isGroup') === 'true';
            $reference = request()->input('reference');
            
            Log::info('Loan parameters', [
                'is_group' => $isGroup,
                'reference' => $reference
            ]);
            
            if ($isGroup && $reference) {
                $pcrenouvs = PCRenouv::where('reference', $reference)
                                  ->where('statut', 'en stock')
                                  ->with('stocks')
                                  ->get();
                
                $totalQuantity = $pcrenouvs->sum(function($r) {
                    return $r->stocks->first()?->pivot->quantite ?? 0;
                });
                
                Log::info('Group loan details', [
                    'reference' => $reference,
                    'total_quantity' => $totalQuantity,
                    'items_count' => $pcrenouvs->count()
                ]);
                
                $pcrenouv = $pcrenouvs->first();
                $pcrenouv->isGroup = true;
                $pcrenouv->totalQuantity = $totalQuantity;
                $pcrenouv->groupItems = $pcrenouvs;
            } else {
                $pcrenouv = PCRenouv::with('stocks')->findOrFail($id);
                Log::info('Single item loan', [
                    'id' => $id,
                    'reference' => $pcrenouv->reference,
                    'quantity' => $pcrenouv->stocks->first()?->pivot->quantite
                ]);
            }
            
            $type = PCRenouv::TYPES;
            $statut = PCRenouv::STATUTS;
            $clients = Client::all();
            
            return view('gestrenouv.louer', compact('pcrenouv', 'type', 'statut', 'clients'));
        } catch (\Exception $e) {
            Log::error('Error in loan process', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Erreur lors de la préparation du prêt: ' . $e->getMessage());
        }
    }

    public function preter($id)
    {
        try {
            Log::info('Starting lending process', ['id' => $id]);
            
            $isGroup = request()->has('isGroup') && request()->input('isGroup') === 'true';
            $reference = request()->input('reference');
            
            Log::info('Lending parameters', [
                'is_group' => $isGroup,
                'reference' => $reference
            ]);
            
            if ($isGroup && $reference) {
                $pcrenouvs = PCRenouv::where('reference', $reference)
                                  ->where('statut', 'en stock')
                                  ->with('stocks')
                                  ->get();
                
                $totalQuantity = $pcrenouvs->sum(function($r) {
                    return $r->stocks->first()?->pivot->quantite ?? 0;
                });
                
                Log::info('Group lending details', [
                    'reference' => $reference,
                    'total_quantity' => $totalQuantity,
                    'items_count' => $pcrenouvs->count()
                ]);
                
                $pcrenouv = $pcrenouvs->first();
                $pcrenouv->isGroup = true;
                $pcrenouv->totalQuantity = $totalQuantity;
                $pcrenouv->groupItems = $pcrenouvs;
            } else {
                $pcrenouv = PCRenouv::with('stocks')->findOrFail($id);
                Log::info('Single item lending', [
                    'id' => $id,
                    'reference' => $pcrenouv->reference,
                    'quantity' => $pcrenouv->stocks->first()?->pivot->quantite
                ]);
            }
            
            $type = PCRenouv::TYPES;
            $statut = PCRenouv::STATUTS;
            $clients = Client::all();
            
            return view('gestrenouv.preter', compact('pcrenouv', 'type', 'statut', 'clients'));
        } catch (\Exception $e) {
            Log::error('Error in lending process', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Erreur lors de la préparation du prêt: ' . $e->getMessage());
        }
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
            Log::info('Starting addLocPret process', [
                'id' => $id,
                'is_group' => $request->input('is_group'),
                'data' => $validated
            ]);

            $isGroup = $request->has('is_group') && $request->input('is_group');
            
            // Process client
            $clientId = null;
            
            if (isset($validated['new_client']) && !empty($validated['new_client']['nom'])) {
                $client = Client::create([
                    'nom' => $validated['new_client']['nom'],
                    'code_client' => $validated['new_client']['code_client'] ?? null,
                ]);
                $clientId = $client->id;
                Log::info('Created new client', [
                    'client_id' => $clientId,
                    'nom' => $client->nom
                ]);
            } elseif (!empty($validated['client_id'])) {
                $clientId = $validated['client_id'];
                Log::info('Using existing client', ['client_id' => $clientId]);
            }
            
            // Create LocPret record
            $locPret = LocPret::create([
                'date_pret' => $validated['date_pret'] ?? now(),
                'date_retour' => $validated['date_retour'] ?? null,
                'client_id' => $clientId,
            ]);
            
            Log::info('Created LocPret record', [
                'id' => $locPret->id,
                'client_id' => $clientId
            ]);
            
            if ($isGroup) {
                $this->processGroupRental($validated, $locPret);
            } else {
                $this->processSingleRental($id, $validated, $locPret);
            }
            
            DB::commit();
            Log::info('Transaction completed successfully');
            return redirect()->route('gestrenouv.index')->with('success', 'PCRenouv ' . $validated['statut'] . ' avec succès!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in addLocPret process', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Process a group rental operation
     */
    private function processGroupRental($validated, $locPret)
    {
        $originalReference = preg_replace('/^(prêt-|location-)/', '', $validated['reference']);
        $originalReference = preg_replace('/-\d+$/', '', $originalReference);
        
        Log::info('Processing group operation', [
            'original_reference' => $originalReference
        ]);
        
        $pcrenouvs = PCRenouv::where('reference', $originalReference)
                         ->where('statut', 'en stock')
                         ->with('stocks')
                         ->get();
        
        $totalAvailableQuantity = $pcrenouvs->sum(function($r) {
            return $r->stocks->first()?->pivot->quantite ?? 0;
        });
        
        Log::info('Group availability check', [
            'requested_quantity' => $validated['quantite'],
            'available_quantity' => $totalAvailableQuantity
        ]);
        
        if ($validated['quantite'] > $totalAvailableQuantity) {
            throw new \Exception("Quantité demandée ($validated[quantite]) supérieure à la quantité disponible ($totalAvailableQuantity)");
        }
        
        // Create a new PCRenouv for the rental
        $loanedPcrenouv = PCRenouv::create([
            'reference' => $validated['reference'],
            'numero_serie' => $validated['numero_serie'],
            'caracteristiques' => $validated['caracteristiques'],
            'type' => $validated['type'],
            'statut' => $validated['statut'],
            'employe_id' => auth()->id(),
            'quantite' => $validated['quantite'],
            'locPret_id' => $locPret->id,
        ]);
        
        Log::info('Created group loan/lend record', [
            'id' => $loanedPcrenouv->id,
            'reference' => $loanedPcrenouv->reference,
            'locpret_id' => $locPret->id
        ]);
        
        $loanedPcrenouv->stocks()->attach($validated['stock_id'], ['quantite' => $validated['quantite']]);
        
        // Reduce quantities from original PCRenouv records
        $remainingToReduce = $validated['quantite'];
        
        foreach ($pcrenouvs as $pcrenouv) {
            $currentQty = $pcrenouv->stocks->first()?->pivot->quantite ?? 0;
            
            if ($currentQty <= 0) continue;
            
            $toReduce = min($remainingToReduce, $currentQty);
            $newQty = $currentQty - $toReduce;
            
            Log::info('Updating stock quantities', [
                'pcrenouv_id' => $pcrenouv->id,
                'current_quantity' => $currentQty,
                'to_reduce' => $toReduce,
                'new_quantity' => $newQty
            ]);
            
            if ($newQty <= 0) {
                $pcrenouv->stocks()->detach();
                $pcrenouv->delete();
                Log::info('Removed empty stock record', ['pcrenouv_id' => $pcrenouv->id]);
            } else {
                $pcrenouv->stocks()->updateExistingPivot(
                    $pcrenouv->stocks->first()->id,
                    ['quantite' => $newQty]
                );
                Log::info('Updated stock quantity', [
                    'pcrenouv_id' => $pcrenouv->id,
                    'new_quantity' => $newQty
                ]);
            }
            
            $remainingToReduce -= $toReduce;
            if ($remainingToReduce <= 0) break;
        }
    }
    
    /**
     * Process a single rental operation
     */
    private function processSingleRental($id, $validated, $locPret)
    {
        $originalPcrenouv = PCRenouv::with('stocks')->findOrFail($id);
        
        $totalQuantity = $originalPcrenouv->stocks->first()?->pivot->quantite ?? 0;
        
        Log::info('Single item availability check', [
            'id' => $id,
            'requested_quantity' => $validated['quantite'],
            'available_quantity' => $totalQuantity
        ]);
        
        if ($validated['quantite'] > $totalQuantity) {
            throw new \Exception("Quantité demandée ($validated[quantite]) supérieure à la quantité disponible ($totalQuantity)");
        }
        
        // Create a new PCRenouv for the rental
        $loanedPcrenouv = PCRenouv::create([
            'reference' => $validated['reference'],
            'numero_serie' => $validated['numero_serie'],
            'caracteristiques' => $validated['caracteristiques'],
            'type' => $validated['type'],
            'statut' => $validated['statut'],
            'employe_id' => auth()->id(),
            'quantite' => $validated['quantite'],
            'locPret_id' => $locPret->id,
        ]);
        
        Log::info('Created single loan/lend record', [
            'id' => $loanedPcrenouv->id,
            'reference' => $loanedPcrenouv->reference,
            'locpret_id' => $locPret->id
        ]);
        
        $loanedPcrenouv->stocks()->attach($validated['stock_id'], ['quantite' => $validated['quantite']]);
        
        // Update original PCRenouv quantity
        $remainingQuantity = $totalQuantity - $validated['quantite'];
        
        if ($remainingQuantity <= 0) {
            $originalPcrenouv->stocks()->detach();
            $originalPcrenouv->delete();
            Log::info('Removed original record (no remaining quantity)', ['id' => $id]);
        } else {
            $originalPcrenouv->stocks()->updateExistingPivot(
                $originalPcrenouv->stocks->first()->id,
                ['quantite' => $remainingQuantity]
            );
            Log::info('Updated original record quantity', [
                'id' => $id,
                'new_quantity' => $remainingQuantity
            ]);
        }
    }

    public function retour(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            Log::info('Starting return process', ['id' => $id]);

            $pcrenouv = PCRenouv::with(['stocks', 'clients', 'locPret'])->findOrFail($id);
            
            if (!in_array(strtolower($pcrenouv->statut), ['loué', 'prêté'])) {
                throw new \Exception('Ce PCRenouv n\'est pas prêté ou loué.');
            }
            
            $reference = $pcrenouv->reference;
            $baseReference = preg_replace('/^(prêt-|location-)/', '', $reference);
            $baseReference = preg_replace('/-\d+$/', '', $baseReference);
            
            Log::info('Return details', [
                'original_reference' => $reference,
                'base_reference' => $baseReference
            ]);
            
            $originalPCRenouv = PCRenouv::where('reference', $baseReference)
                                        ->where('statut', 'en stock')
                                        ->with('stocks')
                                        ->first();
            
            $returnQuantity = $pcrenouv->quantite ?? 0;
            
            Log::info('Return quantity', [
                'quantity' => $returnQuantity,
                'original_exists' => (bool)$originalPCRenouv
            ]);
            
            if ($originalPCRenouv) {
                // Add returned quantity to existing PCRenouv
                $originalPCRenouv->update([
                    'quantite' => $originalPCRenouv->quantite + $returnQuantity
                ]);
                
                Log::info('Updated original PCRenouv quantity', [
                    'pcrenouv_id' => $originalPCRenouv->id,
                    'new_quantity' => $originalPCRenouv->quantite
                ]);
            } else {
                // Create new PCRenouv with returned quantity
                $newPCRenouv = PCRenouv::create([
                    'reference' => $baseReference,
                    'numero_serie' => preg_replace('/^(prêt-|location-)/', '', $pcrenouv->numero_serie),
                    'caracteristiques' => $pcrenouv->caracteristiques,
                    'type' => $pcrenouv->type,
                    'statut' => 'en stock',
                    'employe_id' => auth()->id(),
                    'quantite' => $returnQuantity,
                    'locPret_id' => null,
                ]);
                
                // Attach to the same stock
                if ($pcrenouv->stocks->isNotEmpty()) {
                    $newPCRenouv->stocks()->attach($pcrenouv->stocks->first()->id, [
                        'quantite' => $returnQuantity
                    ]);
                }
                
                Log::info('Created new PCRenouv for return', [
                    'id' => $newPCRenouv->id,
                    'reference' => $newPCRenouv->reference,
                    'quantity' => $returnQuantity
                ]);
            }
            
            // Check if this is the last PCRenouv linked to the LocPret
            $locPretId = $pcrenouv->locPret_id;
            $locPret = null;
            
            if ($locPretId) {
                $locPret = LocPret::findOrFail($locPretId);
                $remainingPCs = PCRenouv::where('locPret_id', $locPretId)
                                       ->where('id', '!=', $pcrenouv->id)
                                       ->count();
                
                Log::info('Checking remaining PCs in LocPret', [
                    'locpret_id' => $locPretId,
                    'remaining_pcs' => $remainingPCs
                ]);
                
                if ($remainingPCs == 0) {
                    // This was the last PC, delete the LocPret record
                    $locPret->delete();
                    Log::info('Deleted LocPret record (all PCs returned)', ['locpret_id' => $locPretId]);
                }
            }
            
            // Delete the returned PCRenouv
            $pcrenouv->delete();
            Log::info('Deleted loan/lend record', ['id' => $id]);
            
            DB::commit();
            return redirect()->route('gestrenouv.index')->with('success', 'PCRenouv retourné avec succès!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in return process', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Erreur lors du retour: ' . $e->getMessage());
        }
    }
}