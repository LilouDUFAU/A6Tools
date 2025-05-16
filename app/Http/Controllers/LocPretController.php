<?php

namespace App\Http\Controllers;

use App\Models\LocPret;
use App\Models\PCRenouv;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocPretController extends Controller
{
    public function index()
    {
        $locPrets = LocPret::with(['clients', 'pcrenouvs'])->get();
        return view('locpret.index', compact('locPrets'));
    }

    public function create()
    {
        $clients = Client::all();
        $pcrenouvs = PCRenouv::where('statut', 'en stock')->get();
        return view('locpret.create', compact('clients', 'pcrenouvs'));
    }


public function store(Request $request)
{
    // Validation initiale pour récupérer le type d'opération (pour la création des PC)
    $typeOperation = $request->validate([
        'type_operation' => 'required|in:prêt,location',
    ])['type_operation'];

    // Gestion du client (création ou utilisation existant)
    if ($request->has('new_client') && !$request->filled('client_id')) {
        // Validation pour le nouveau client
        $validatedClient = $request->validate([
            'new_client.nom' => 'required|string|max:255',
            'new_client.code_client' => 'required|string|max:255|unique:clients,code_client',
            'new_client.numero_telephone' => 'nullable|string|max:20',
        ]);

        // Création du client
        $client = Client::create([
            'nom' => $validatedClient['new_client']['nom'],
            'code_client' => $validatedClient['new_client']['code_client'],
            'numero_telephone' => $validatedClient['new_client']['numero_telephone'] ?? null,
        ]);

        // Utilisation de l'ID du nouveau client
        $clientId = $client->id;
    } else {
        // Validation pour un client existant
        $validatedClient = $request->validate([
            'client_id' => 'required|exists:clients,id',
        ]);
        
        $clientId = $validatedClient['client_id'];
    }

    // Validation des autres champs
    $validated = $request->validate([
        'date_debut' => 'required|date',
        'date_retour' => 'required|date|after_or_equal:date_debut',
        'pcrenouv_ids' => 'required|array',
        'pcrenouv_ids.*' => 'exists:p_c_renouvs,id',
    ]);

    DB::beginTransaction();
    try {
        $locPret = LocPret::create([
            'client_id' => $clientId,
            'date_debut' => $validated['date_debut'],
            'date_retour' => $validated['date_retour'],
        ]);

        $locPret->pcrenouvs()->attach($validated['pcrenouv_ids']);

        PCRenouv::whereIn('id', $validated['pcrenouv_ids'])->update([
            'statut' => $typeOperation === 'prêt' ? 'prêté' : 'loué'
        ]);

        DB::commit();
        return redirect()->route('locpret.index')->with('success', 'Location/Prêt créé avec succès.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur lors de la création: ' . $e->getMessage());
        return back()->withInput()->with('error', 'Erreur: ' . $e->getMessage());
    }
}
    public function show($id)
    {
        $locPret = LocPret::with(['clients', 'pcrenouvs'])->findOrFail($id);
        return view('locpret.show', compact('locPret'));
    }

    public function edit($id)
    {
        $locPret = LocPret::with('pcrenouvs')->findOrFail($id);
        $clients = Client::all();

        $pcrenouvs = PCRenouv::where('statut', 'en stock')
            ->orWhereHas('locprets', function ($query) use ($id) {
                $query->where('loc_pret_id', $id);
            })->get();

        return view('locpret.edit', compact('locPret', 'clients', 'pcrenouvs'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date_debut' => 'required|date',
            'date_retour' => 'required|date|after_or_equal:date_debut',
            'pcrenouv_ids' => 'required|array',
            'pcrenouv_ids.*' => 'exists:p_c_renouvs,id',
            'type_operation' => 'required|in:prêt,location',
        ]);

        DB::beginTransaction();
        try {
            $locPret = LocPret::findOrFail($id);

            $locPret->update([
                'client_id' => $validated['client_id'],
                'date_debut' => $validated['date_debut'],
                'date_retour' => $validated['date_retour'],
            ]);

            // Réinitialiser l’ancien statut des PC associés
            $currentIds = $locPret->pcrenouvs->pluck('id')->toArray();
            PCRenouv::whereIn('id', $currentIds)->update(['statut' => 'en stock']);

            // Synchroniser les nouveaux PC
            $locPret->pcrenouvs()->sync($validated['pcrenouv_ids']);

            PCRenouv::whereIn('id', $validated['pcrenouv_ids'])->update([
                'statut' => $validated['type_operation'] === 'prêt' ? 'prêté' : 'loué'
            ]);

            DB::commit();
            return redirect()->route('locpret.index')->with('success', 'Location/Prêt mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $locPret = LocPret::findOrFail($id);
            $pcrenouvIds = $locPret->pcrenouvs->pluck('id');

            PCRenouv::whereIn('id', $pcrenouvIds)->update(['statut' => 'en stock']);
            $locPret->pcrenouvs()->detach();
            $locPret->delete();

            DB::commit();
            return redirect()->route('locpret.index')->with('success', 'Location/Prêt supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    public function retourner($id)
    {
        DB::beginTransaction();
        try {
            $locPret = LocPret::findOrFail($id);
            $pcrenouvIds = $locPret->pcrenouvs->pluck('id');

            if ($pcrenouvIds->isEmpty()) {
                return back()->with('error', 'Aucun PC n\'est associé à cette location/prêt.');
            }

            PCRenouv::whereIn('id', $pcrenouvIds)->update(['statut' => 'en stock']);
            $locPret->pcrenouvs()->detach();
            $locPret->delete();

            DB::commit();
            return redirect()->route('locpret.index')->with('success', 'Retour effectué avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du retour: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'opération de retour.');
        }
    }
}
