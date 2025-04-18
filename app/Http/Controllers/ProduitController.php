<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    /**
     * Affiche la liste de tous les produits.
     */
    public function index()
    {
        $produits = Produit::all();
        return view('produits.index', compact('produits'));
    }

    /**
     * Affiche le formulaire de création d’un produit.
     */
    public function create()
    {
        return view('produits.create');
    }

    /**
     * Enregistre un nouveau produit.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'reference' => 'required|string|unique:produits,reference',
            'prix_referencement' => 'required|numeric|min:0',
            'lien_produit_fournisseur' => 'nullable|url|max:1000',
            'date_livraison_fournisseur' => 'nullable|date',
        ]);

        Produit::create($validated);

        return redirect()->route('produits.index')->with('success', 'Produit créé avec succès.');
    }

    /**
     * Affiche un produit spécifique.
     */
    public function show(string $id)
    {
        $produit = Produit::findOrFail($id);
        return view('produits.show', compact('produit'));
    }

    /**
     * Affiche le formulaire d’édition d’un produit.
     */
    public function edit(string $id)
    {
        $produit = Produit::findOrFail($id);
        return view('produits.edit', compact('produit'));
    }

    /**
     * Met à jour un produit.
     */
    public function update(Request $request, string $id)
    {
        $produit = Produit::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'reference' => 'required|string|unique:produits,reference,' . $produit->id,
            'prix_referencement' => 'required|numeric|min:0',
            'lien_produit_fournisseur' => 'nullable|url|max:1000',
            'date_livraison_fournisseur' => 'nullable|date',
        ]);

        $produit->update($validated);

        return redirect()->route('produits.index')->with('success', 'Produit mis à jour avec succès.');
    }

    /**
     * Supprime un produit.
     */
    public function destroy(string $id)
    {
        $produit = Produit::findOrFail($id);
        $produit->delete();

        return redirect()->route('produits.index')->with('success', 'Produit supprimé avec succès.');
    }
}
