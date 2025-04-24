<?php

namespace App\Http\Controllers;

use App\Models\Panne;
use App\Models\Fournisseur;
use App\Models\Client;
use Illuminate\Http\Request;

class PanneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pannes = Panne::with(['fournisseur', 'clients'])->get();
        return view('gestsav.index', compact('pannes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fournisseurs = Fournisseur::all();
        $clients = Client::all();
        return view('pannes.create', compact('fournisseurs', 'clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'etat_client' => 'required|in:ordi de pret,échangé,en attente',
            'categorie_materiel' => 'required|string',
            'categorie_panne' => 'required|string',
            'detail_panne' => 'required|string',
            'date_panne' => 'required|date',
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'client_ids' => 'array|exists:clients,id'
        ]);

        $panne = Panne::create($validated);

        if ($request->has('client_ids')) {
            $panne->clients()->sync($validated['client_ids']);
        }

        return redirect()->route('pannes.index')->with('success', 'Panne créée avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $panne = Panne::with(['fournisseur', 'clients'])->findOrFail($id);
        return view('pannes.show', compact('panne'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $panne = Panne::findOrFail($id);
        $fournisseurs = Fournisseur::all();
        $clients = Client::all();
        return view('pannes.edit', compact('panne', 'fournisseurs', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'etat_client' => 'required|in:ordi de pret,échangé,en attente',
            'categorie_materiel' => 'required|string',
            'categorie_panne' => 'required|string',
            'detail_panne' => 'required|string',
            'date_panne' => 'required|date',
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'client_ids' => 'array|exists:clients,id'
        ]);

        $panne = Panne::findOrFail($id);
        $panne->update($validated);

        if ($request->has('client_ids')) {
            $panne->clients()->sync($validated['client_ids']);
        }

        return redirect()->route('pannes.index')->with('success', 'Panne mise à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $panne = Panne::findOrFail($id);
        $panne->clients()->detach();
        $panne->delete();

        return redirect()->route('pannes.index')->with('success', 'Panne supprimée avec succès');
    }
}
