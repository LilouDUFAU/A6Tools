<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PCRenouv;
use App\Models\User;
use App\Models\Stock;
use App\Models\StockRenouv;
use Illuminate\Support\Facades\DB;

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

        // Mettre à jour la relation pivot stock_quantité
        $pcrenouv->stocks()->sync([
            $request->input('stock_id') => ['quantite' => $request->input('quantite')]
        ]);

        return redirect()->route('gestrenouv.index')->with('success', 'PCRenouv mis à jour avec succès.');
    }

    public function destroy(string $id)
    {
        $pcrenouv = PCRenouv::findOrFail($id);

        // Détacher les relations avec les stocks
        $pcrenouv->stocks()->detach();

        // Supprimer l'enregistrement
        $pcrenouv->delete();

        return redirect()->route('gestrenouv.index')->with('success', 'PCRenouv supprimé avec succès.');
    }
}
