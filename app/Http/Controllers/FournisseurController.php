<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;

class FournisseurController extends Controller
{
    /**
     * Afficher la liste des fournisseurs.
     */
    public function index()
    {
        $fournisseurs = Fournisseur::all();
        return view('fournisseurs.index', compact('fournisseurs'));
    }

    /**
     * Afficher le formulaire de création d'un fournisseur.
     */
    public function create()
    {
        return view('fournisseurs.create');
    }

    /**
     * Enregistrer un nouveau fournisseur.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        Fournisseur::create($validated);

        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur créé avec succès.');
    }

    /**
     * Afficher un fournisseur spécifique.
     */
    public function show(string $id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        return view('fournisseurs.show', compact('fournisseur'));
    }

    /**
     * Afficher le formulaire de modification d'un fournisseur.
     */
    public function edit(string $id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        return view('fournisseurs.edit', compact('fournisseur'));
    }

    /**
     * Mettre à jour les informations d'un fournisseur.
     */
    public function update(Request $request, string $id)
    {
        $fournisseur = Fournisseur::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        $fournisseur->update($validated);

        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur mis à jour avec succès.');
    }

    /**
     * Supprimer un fournisseur.
     */
    public function destroy(string $id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        $fournisseur->delete();

        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur supprimé avec succès.');
    }
}