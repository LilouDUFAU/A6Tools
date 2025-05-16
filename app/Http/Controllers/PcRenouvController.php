<?php

namespace App\Http\Controllers;

use App\Models\PCRenouv;
use App\Models\Stock;
use App\Models\Client;
use App\Models\LocPret;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PCRenouvController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pcrenouvs = PCRenouv::with(['locprets.clients', 'stocks'])->get();
        return view('gestrenouv.index', compact('pcrenouvs'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $stocks = Stock::all();
        $types = PCRenouv::TYPES;
        $statuts = PCRenouv::STATUTS;
        
        return view('gestrenouv.create', compact('stocks', 'types', 'statuts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_serie' => 'required|string|max:255|unique:p_c_renouvs',
            'reference' => 'required|string|max:255',
            'quantite' => 'required|integer|min:1',
            'caracteristiques' => 'required|string',
            'type' => 'required|in:' . implode(',', PCRenouv::TYPES),
            'statut' => 'required|in:' . implode(',', PCRenouv::STATUTS),
            'stock_id' => 'required|exists:stocks,id',
            'stock_quantite' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $pcrenouv = PCRenouv::create([
                'numero_serie' => $validated['numero_serie'],
                'reference' => $validated['reference'],
                'quantite' => $validated['quantite'],
                'caracteristiques' => $validated['caracteristiques'],
                'type' => $validated['type'],
                'statut' => $validated['statut']
            ]);

            // Associer au stock
            $pcrenouv->stocks()->attach($validated['stock_id'], ['quantite' => $validated['stock_quantite']]);
            
            DB::commit();
            return redirect()->route('gestrenouv.index')->with('success', 'PC Renouv créé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création du PC Renouv: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erreur lors de la création du PC Renouv.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pcRenouv = PCRenouv::with('stocks', 'locPrets.clients')->findOrFail($id);
        return view('gestrenouv.show', compact('pcRenouv'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pcRenouv = PCRenouv::with('stocks', 'locPrets.clients')->findOrFail($id);
        $stocks = Stock::all();
        $types = PCRenouv::TYPES;
        $statuts = PCRenouv::STATUTS;

        return view('gestrenouv.edit', compact('pcRenouv', 'stocks', 'types', 'statuts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pcRenouv = PCRenouv::findOrFail($id);

        $validated = $request->validate([
            'numero_serie' => 'required|string|max:255|unique:p_c_renouvs,numero_serie,' . $pcRenouv->id,
            'reference' => 'required|string|max:255',
            'quantite' => 'required|integer|min:1',
            'caracteristiques' => 'required|string',
            'type' => 'required|in:' . implode(',', PCRenouv::TYPES),
            'statut' => 'required|in:' . implode(',', PCRenouv::STATUTS),
            'stock_id' => 'required|exists:stocks,id',
            'stock_quantite' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $pcRenouv->update([
                'numero_serie' => $validated['numero_serie'],
                'reference' => $validated['reference'],
                'quantite' => $validated['quantite'],
                'caracteristiques' => $validated['caracteristiques'],
                'type' => $validated['type'],
                'statut' => $validated['statut']
            ]);

            // Mise à jour de la relation avec les stocks
            $pcRenouv->stocks()->detach();
            $pcRenouv->stocks()->attach($validated['stock_id'], ['quantite' => $validated['stock_quantite']]);

            DB::commit();

            return redirect()->route('gestrenouv.index')->with('success', 'PC Renouv mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour du PC Renouv: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erreur lors de la mise à jour du PC Renouv.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $pcRenouv = PCRenouv::findOrFail($id);

            // Vérifier si le PC est actuellement en prêt/location
            if ($pcRenouv->locPrets()->exists()) {
                return back()->with('error', 'Impossible de supprimer ce PC car il est actuellement en prêt ou en location.');
            }
            
            // Détacher des stocks
            $pcRenouv->stocks()->detach();
            
            // Supprimer le PC
            $pcRenouv->delete();
            
            DB::commit();
            return redirect()->route('gestrenouv.index')->with('success', 'PC Renouv supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression du PC Renouv: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la suppression du PC Renouv.');
        }
    }
}
