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

    public function louer(){}

    public function preter(){}

    public function retour(){}
}