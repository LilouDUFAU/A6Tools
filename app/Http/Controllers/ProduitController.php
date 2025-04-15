<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Afficher tous les produits
        $produits = Produit::all(); // On récupère tous les produits
        return view('produits.index', compact('produits')); // Affichage de la vue des produits
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Afficher le formulaire de création d'un produit
        return view('produits.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Valider les données envoyées
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'caracteristiques_techniques' => 'required|string',
            'reference' => 'required|string|unique:produits,reference',
            'quantite_stock' => 'required|integer|min:0',
            'quantite_client' => 'required|integer|min:0',
            'prix' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Vérification pour l'image
        ]);

        // Traiter l'image si elle est envoyée
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('produits_images', 'public');
            $validated['image'] = $imagePath;
        }

        // Créer un produit avec les données validées
        Produit::create($validated);

        // Retourner à la liste des produits avec un message de succès
        return redirect()->route('produits.index')->with('success', 'Produit créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Afficher les détails d'un produit spécifique
        $produit = Produit::findOrFail($id);
        return view('produits.show', compact('produit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Récupérer un produit pour l'éditer
        $produit = Produit::findOrFail($id);
        return view('produits.edit', compact('produit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Valider les données envoyées
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'caracteristiques_techniques' => 'required|string',
            'reference' => 'required|string|unique:produits,reference,' . $id,
            'quantite_stock' => 'required|integer|min:0',
            'quantite_client' => 'required|integer|min:0',
            'prix' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Vérification pour l'image
        ]);

        // Récupérer le produit à mettre à jour
        $produit = Produit::findOrFail($id);

        // Traiter l'image si elle est envoyée
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($produit->image) {
                \Storage::delete('public/' . $produit->image);
            }
            $imagePath = $request->file('image')->store('produits_images', 'public');
            $validated['image'] = $imagePath;
        }

        // Mettre à jour les données du produit
        $produit->update($validated);

        // Retourner à la liste des produits avec un message de succès
        return redirect()->route('produits.index')->with('success', 'Produit mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Récupérer le produit à supprimer
        $produit = Produit::findOrFail($id);

        // Supprimer l'image si elle existe
        if ($produit->image) {
            \Storage::delete('public/' . $produit->image);
        }

        // Supprimer le produit de la base de données
        $produit->delete();

        // Retourner à la liste des produits avec un message de succès
        return redirect()->route('produits.index')->with('success', 'Produit supprimé avec succès.');
    }
}
