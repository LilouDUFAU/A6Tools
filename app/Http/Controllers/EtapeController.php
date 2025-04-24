<?php
namespace App\Http\Controllers;

use App\Models\Etape;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class EtapeController extends Controller
{
    /**
     * Affiche une liste de toutes les étapes.
     */
    public function index()
    {
        // Récupérer toutes les étapes
        $etapes = Etape::all();
        return view('etapes.index', compact('etapes'));
    }

    /**
     * Montre le formulaire pour créer une nouvelle étape.
     */
    public function create()
    {
        return view('etapes.create');
    }

    /**
     * Enregistre une nouvelle étape dans la base de données.
     */
    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'intitule' => 'required|string|max:255',  // Validation pour le champ 'intitule'
            'is_done' => 'required|boolean',  // Validation pour 'is_done'
        ]);

        // Créer une nouvelle étape
        Etape::create([
            'intitule' => $validated['intitule'],
            'is_done' => $validated['is_done'],
        ]);

        // Retourner à la liste des étapes avec un message de succès
        return redirect()->route('etapes.index')->with('success', 'Étape créée avec succès.');
    }

    /**
     * Affiche les détails d'une étape spécifique.
     */
    public function show(string $id)
    {
        // Récupérer l'étape
        $etape = Etape::findOrFail($id);

        return view('etapes.show', compact('etape'));
    }

    /**
     * Montre le formulaire pour éditer une étape spécifique.
     */
    public function edit(string $id)
    {
        // Récupérer l'étape à éditer
        $etape = Etape::findOrFail($id);

        return view('etapes.edit', compact('etape'));
    }

    /**
     * Met à jour l'état de l'étape (is_done).
     */
    public function update(Request $request, $id)
    {
        // Validation de la requête
        $request->validate([
            'is_done' => 'required|boolean',  // On vérifie que la valeur de is_done est un booléen
        ]);

        // Log pour vérifier que la requête est bien reçue
        Log::info("Mise à jour de l'état de l'étape ID : $id avec is_done = " . $request->input('is_done'));

        // Récupérer l'étape à partir de l'ID
        $etape = Etape::findOrFail($id);

        // Log pour vérifier si l'étape existe
        Log::info("Étape trouvée : " . $etape->intitule);

        // Mise à jour de l'état de l'étape
        $etape->is_done = $request->input('is_done');
        $etape->save();

        // Log après la mise à jour
        Log::info("L'état de l'étape ID $id a été mis à jour à : " . $etape->is_done);

        // Réponse JSON avec le nouvel état de l'étape
        return redirect()->back()->with('success', 'L\'état de l\'étape a été mis à jour avec succès.');
    }

    
    /**
     * Supprime une étape spécifique.
     */
    public function destroy(string $id)
    {
        // Trouver l'étape par son ID
        $etape = Etape::findOrFail($id);

        // Supprimer l'étape
        $etape->delete();

        // Retourner une réponse de succès
        return redirect()->route('etapes.index')->with('success', 'Étape supprimée avec succès.');
    }
}
