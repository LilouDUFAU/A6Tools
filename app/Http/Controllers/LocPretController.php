<?php

namespace App\Http\Controllers;

use App\Models\LocPret;
use App\Models\PCRenouv;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LocPretController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locPrets = LocPret::with(['client', 'pcRenouv'])->get();
        return view('locpret.index', compact('locPrets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::all();
        $pcrenouvs = PCRenouv::where('statut', 'en stock')->get();
        return view('locpret.create', compact('clients', 'pcrenouvs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_pret' => 'required|date',
            'date_retour' => 'required|date|after_or_equal:date_pret',
            'client_id' => 'required|exists:clients,id',
            'pcrenouv_ids' => 'required|array',
            'pcrenouv_ids.*' => 'exists:p_c_renouvs,id',
        ]);

        DB::beginTransaction();
        try {
            Log::info('Creating new LocPret', $validated);

            $locPret = LocPret::create([
                'date_pret' => $validated['date_pret'],
                'date_retour' => $validated['date_retour'],
                'client_id' => $validated['client_id'],
            ]);

            Log::info('LocPret created successfully', ['id' => $locPret->id]);

            foreach ($validated['pcrenouv_ids'] as $pcrenouvId) {
                $pcrenouv = PCRenouv::findOrFail($pcrenouvId);
                $pcrenouv->update([
                    'statut' => 'prêté',
                    'locPret_id' => $locPret->id,
                ]);
                
                Log::info('PCRenouv linked to LocPret', [
                    'pcrenouv_id' => $pcrenouv->id,
                    'locpret_id' => $locPret->id
                ]);
            }

            DB::commit();
            return redirect()->route('locpret.index')->with('success', 'Location/Prêt créé avec succès!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating LocPret', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            return redirect()->back()->with('error', 'Erreur lors de la création de la location/prêt: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $locPret = LocPret::with(['client', 'pcRenouv'])->findOrFail($id);
        return view('locpret.show', compact('locPret'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $locPret = LocPret::with(['client', 'pcRenouv'])->findOrFail($id);
        $clients = Client::all();
        $pcrenouvs = PCRenouv::where('statut', 'en stock')
            ->orWhere('locPret_id', $id)
            ->get();
        
        return view('locpret.edit', compact('locPret', 'clients', 'pcrenouvs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'date_pret' => 'required|date',
            'date_retour' => 'required|date|after_or_equal:date_pret',
            'client_id' => 'required|exists:clients,id',
            'pcrenouv_ids' => 'required|array',
            'pcrenouv_ids.*' => 'exists:p_c_renouvs,id',
        ]);

        DB::beginTransaction();
        try {
            Log::info('Updating LocPret', ['id' => $id, 'data' => $validated]);

            $locPret = LocPret::findOrFail($id);
            
            $locPret->update([
                'date_pret' => $validated['date_pret'],
                'date_retour' => $validated['date_retour'],
                'client_id' => $validated['client_id'],
            ]);

            Log::info('LocPret updated successfully', ['id' => $locPret->id]);

            // Reset PCRenouv items not in the new list
            PCRenouv::where('locPret_id', $id)
                ->whereNotIn('id', $validated['pcrenouv_ids'])
                ->update([
                    'statut' => 'en stock',
                    'locPret_id' => null,
                ]);
            
            // Update PCRenouv items in the new list
            foreach ($validated['pcrenouv_ids'] as $pcrenouvId) {
                $pcrenouv = PCRenouv::findOrFail($pcrenouvId);
                $pcrenouv->update([
                    'statut' => 'prêté',
                    'locPret_id' => $locPret->id,
                ]);
                
                Log::info('PCRenouv updated for LocPret', [
                    'pcrenouv_id' => $pcrenouv->id,
                    'locpret_id' => $locPret->id
                ]);
            }

            DB::commit();
            return redirect()->route('locpret.index')->with('success', 'Location/Prêt mis à jour avec succès!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating LocPret', [
                'id' => $id,
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour de la location/prêt: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            Log::info('Deleting LocPret', ['id' => $id]);
            
            // Reset PCRenouv items linked to this LocPret
            PCRenouv::where('locPret_id', $id)->update([
                'statut' => 'en stock',
                'locPret_id' => null,
            ]);
            
            $locPret = LocPret::findOrFail($id);
            $locPret->delete();
            
            Log::info('LocPret deleted successfully', ['id' => $id]);
            
            DB::commit();
            return redirect()->route('locpret.index')->with('success', 'Location/Prêt supprimé avec succès!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error deleting LocPret', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Erreur lors de la suppression de la location/prêt: ' . $e->getMessage());
        }
    }

    /**
     * Return all PCRenouv items linked to a LocPret.
     */
    public function returnAll(string $id)
    {
        DB::beginTransaction();
        try {
            Log::info('Processing return for all PCRenouv in LocPret', ['locpret_id' => $id]);
            
            $locPret = LocPret::with('pcRenouv')->findOrFail($id);
            
            foreach ($locPret->pcRenouv as $pcrenouv) {
                // Process each PCRenouv return
                $this->processPCRenouvReturn($pcrenouv);
            }
            
            // Delete the LocPret record
            $locPret->delete();
            
            Log::info('All PCRenouv returned and LocPret deleted successfully', ['locpret_id' => $id]);
            
            DB::commit();
            return redirect()->route('locpret.index')->with('success', 'Tous les PC ont été retournés avec succès!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error returning all PCRenouv', [
                'locpret_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Erreur lors du retour des PC: ' . $e->getMessage());
        }
    }

    /**
     * Process the return of a PCRenouv item.
     */
    private function processPCRenouvReturn($pcrenouv)
    {
        Log::info('Processing PCRenouv return', ['id' => $pcrenouv->id]);
        
        $reference = $pcrenouv->reference;
        $baseReference = preg_replace('/^(prêt-|location-)/', '', $reference);
        $baseReference = preg_replace('/-\d+$/', '', $baseReference);
        
        $returnQuantity = $pcrenouv->quantite ?? 0;
        
        // Find original PCRenouv with base reference
        $originalPCRenouv = PCRenouv::where('reference', $baseReference)
                                  ->where('statut', 'en stock')
                                  ->first();
        
        if ($originalPCRenouv) {
            // Update existing PCRenouv quantity
            $originalPCRenouv->update([
                'quantite' => $originalPCRenouv->quantite + $returnQuantity
            ]);
            
            Log::info('Updated original PCRenouv quantity', [
                'pcrenouv_id' => $originalPCRenouv->id,
                'new_quantity' => $originalPCRenouv->quantite
            ]);
        } else {
            // Create new PCRenouv with base reference
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
            
            Log::info('Created new PCRenouv for return', [
                'id' => $newPCRenouv->id,
                'reference' => $newPCRenouv->reference,
                'quantity' => $returnQuantity
            ]);
        }
        
        // Delete the loaned/rented PCRenouv
        $pcrenouv->delete();
        Log::info('Deleted loaned/rented PCRenouv record', ['id' => $pcrenouv->id]);
    }
}