<?php
/**
 * @file FournisseurController.php
 * @brief Contrôleur pour la gestion des fournisseurs dans l'application.
 * @version 1.0
 * @date 2025-04-18
 * @author Lilou DUFAU
 */

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;

/**
 * @class FournisseurController
 * @brief Gère les opérations CRUD pour le modèle Fournisseur.
 */
class FournisseurController extends Controller
{
    /**
     * @brief Affiche la liste de tous les fournisseurs.
     * @return \Illuminate\View\View La vue contenant la liste des fournisseurs.
     */
    public function index()
    {
        // Récupérer tous les fournisseurs depuis la base de données
        $fournisseurs = Fournisseur::all();

        // Retourner la vue avec la liste des fournisseurs
        return view('fournisseurs.index', compact('fournisseurs'));
    }

    /**
     * @brief Affiche le formulaire de création d'un nouveau fournisseur.
     * @return \Illuminate\View\View La vue du formulaire de création.
     */
    public function create()
    {
        // Retourner la vue du formulaire de création
        return view('fournisseurs.create');
    }

    /**
     * @brief Enregistre un nouveau fournisseur dans la base de données.
     * @param \Illuminate\Http\Request $request La requête contenant les données du formulaire.
     * @return \Illuminate\Http\RedirectResponse Redirection vers la liste des fournisseurs avec un message de succès.
     */
    public function store(Request $request)
    {
        // Valider les données du formulaire
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        // Créer un nouveau fournisseur avec les données validées
        Fournisseur::create($validated);

        // Rediriger vers la liste avec un message de succès
        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur créé avec succès.');
    }

    /**
     * @brief Affiche les détails d'un fournisseur spécifique.
     * @param string $id L'identifiant du fournisseur.
     * @return \Illuminate\View\View La vue contenant les détails du fournisseur.
     */
    public function show(string $id)
    {
        // Récupérer le fournisseur par son ID ou échouer si non trouvé
        $fournisseur = Fournisseur::findOrFail($id);

        // Retourner la vue avec les détails du fournisseur
        return view('fournisseurs.show', compact('fournisseur'));
    }

    /**
     * @brief Affiche le formulaire d'édition d'un fournisseur existant.
     * @param string $id L'identifiant du fournisseur.
     * @return \Illuminate\View\View La vue du formulaire d'édition.
     */
    public function edit(string $id)
    {
        // Trouver le fournisseur à modifier
        $fournisseur = Fournisseur::findOrFail($id);

        // Retourner la vue avec les données du fournisseur à éditer
        return view('fournisseurs.edit', compact('fournisseur'));
    }

    /**
     * @brief Met à jour les informations d’un fournisseur existant.
     * @param \Illuminate\Http\Request $request La requête contenant les nouvelles données.
     * @param string $id L'identifiant du fournisseur.
     * @return \Illuminate\Http\RedirectResponse Redirection vers la liste des fournisseurs avec un message de confirmation.
     */
    public function update(Request $request, string $id)
    {
        // Trouver le fournisseur par son ID
        $fournisseur = Fournisseur::findOrFail($id);

        // Valider les nouvelles données du formulaire
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        // Mettre à jour les informations du fournisseur
        $fournisseur->update($validated);

        // Rediriger vers la liste avec un message de confirmation
        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur mis à jour avec succès.');
    }

    /**
     * @brief Supprime un fournisseur de la base de données.
     * @param string $id L'identifiant du fournisseur.
     * @return \Illuminate\Http\RedirectResponse Redirection vers la liste des fournisseurs avec un message de succès.
     */
    public function destroy(string $id)
    {
        // Trouver le fournisseur par son ID
        $fournisseur = Fournisseur::findOrFail($id);

        // Supprimer le fournisseur
        $fournisseur->delete();

        // Rediriger vers la liste avec un message de succès
        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur supprimé avec succès.');
    }
}
