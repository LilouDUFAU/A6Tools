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
    
    /**
     * Prêter ou louer un PC Renouv à un client.
     */
    public function preterLouer(Request $request, PCRenouv $pcRenouv)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date_debut' => 'required|date',
            'date_retour' => 'required|date|after_or_equal:date_debut',
            'type_operation' => 'required|in:prêt,location',
        ]);
        
        DB::beginTransaction();
        try {
            // Vérifier que le PC est disponible
            if ($pcRenouv->statut !== 'en stock') {
                return back()->with('error', 'Ce PC n\'est pas disponible pour prêt ou location.');
            }
            
            // Créer un nouvel enregistrement LocPret
            $locPret = LocPret::create([
                'date_debut' => $validated['date_debut'],
                'date_retour' => $validated['date_retour'],
                'client_id' => $validated['client_id'],
            ]);
            
            // Mettre à jour le statut du PC
            $pcRenouv->update([
                'statut' => $validated['type_operation'] === 'prêt' ? 'prêté' : 'loué',
            ]);
            
            // Associer le LocPret au PC via la table pivot
            $pcRenouv->locPrets()->attach($locPret->id);
            
            DB::commit();
            return redirect()->route('gestrenouv.index')
                ->with('success', 'PC ' . ($validated['type_operation'] === 'prêt' ? 'prêté' : 'loué') . ' avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du prêt/location du PC Renouv: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'opération de prêt/location.');
        }
    }
    
    /**
     * Retourner un PC Renouv prêté ou loué.
     */
    public function retourner(PCRenouv $pcRenouv)
    {
        DB::beginTransaction();
        try {
            // Vérifier que le PC est bien en prêt ou en location
            if (!in_array($pcRenouv->statut, ['prêté', 'loué'])) {
                return back()->with('error', 'Ce PC n\'est pas actuellement en prêt ou en location.');
            }
            
            $locPret = $pcRenouv->locPrets()->first();

            if (!$locPret) {
                return back()->with('error', 'Aucun prêt ou location trouvé pour ce PC.');
            }

            $locPretId = $locPret->id;

            // Mettre à jour le statut du PC
            $pcRenouv->update([
                'statut' => 'en stock',
            ]);

            // Détacher la relation avec LocPret
            $pcRenouv->locPrets()->detach($locPretId);

            // Supprimer l'enregistrement LocPret
            LocPret::findOrFail($locPretId)->delete();

            DB::commit();
            return redirect()->route('gestrenouv.show', $pcRenouv)
                ->with('success', 'PC retourné avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du retour du PC Renouv: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'opération de retour.');
        }
    }
}
