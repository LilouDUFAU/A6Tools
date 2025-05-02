<?php
/**
 * @file EtapeController.php
 * @brief Contrôleur pour la gestion des étapes dans l'application.
 * @version 1.0
 * @date 2025-04-18
 * @author Lilou DUFAU
 */
namespace App\Http\Controllers;

use App\Models\Etape;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * @brief Classe EtapeController
 * @details Gère les opérations CRUD pour le modèle Etape.
 */
class EtapeController extends Controller
{
    /**
     * @brief Affiche la liste de toutes les étapes.
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $etapes = Etape::all();
        return view('etapes.index', compact('etapes'));
    }

    /**
     * @brief Affiche le formulaire de création d'une étape.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('etapes.create');
    }

    /**
     * @brief Enregistre une nouvelle étape dans la base de données.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        // Validation des données d'entrée
        $validated = $request->validate([
            'intitule' => 'required|string|max:255',
            'is_done' => 'required|boolean',
        ]);

        // Vérification de l'unicité de l'intitulé
        Etape::create([
            'intitule' => $validated['intitule'],
            'is_done' => $validated['is_done'],
        ]);

        // Redirection vers la liste des étapes avec un message de succès
        return redirect()->route('etapes.index')->with('success', 'Étape créée avec succès.');
    }

    /**
     * @brief Affiche les détails d'une étape.
     * @param string $id
     * @return \Illuminate\View\View
     * @throws ModelNotFoundException
     */
    public function show(string $id)
    {
        $etape = Etape::findOrFail($id);
        return view('etapes.show', compact('etape'));
    }

    /**
     * @brief Affiche le formulaire d'édition d'une étape.
     * @param string $id
     * @return \Illuminate\View\View
     * @throws ModelNotFoundException
     */
    public function edit(string $id)
    {
        $etape = Etape::findOrFail($id);
        return view('etapes.edit', compact('etape'));
    }

    /**
     * @brief Met à jour l'état (is_done) d'une étape.
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws ModelNotFoundException
     */
    public function update(Request $request, $id)
    {
        // mise à jour de l'état de l'étape
        $etape = Etape::findOrFail($id);
        $etape->is_done = !$etape->is_done;
        $etape->save();

        // Redirection vers la liste des étapes avec un message de succès
        return redirect()->back()->with('success', 'L\'état de l\'étape a été mis à jour.');
    }

    /**
     * @brief Supprime une étape.
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws ModelNotFoundException
     */
    public function destroy(string $id)
    {
        // Suppression de l'étape
        $etape = Etape::findOrFail($id);
        $etape->delete();

        // Redirection vers la liste des étapes avec un message de succès
        return redirect()->route('etapes.index')->with('success', 'Étape supprimée avec succès.');
    }
}
