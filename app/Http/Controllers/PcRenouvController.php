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

        $pcrenouv->stocks()->sync([
            $request->input('stock_id') => ['quantite' => $request->input('quantite')]
        ]);

        return redirect()->route('gestrenouv.index')->with('success', 'PCRenouv mis à jour avec succès.');
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
