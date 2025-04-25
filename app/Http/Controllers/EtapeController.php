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
        // Récupérer l'étape à partir de l'ID
        $etape = Etape::findOrFail($id);
    
        // Log pour vérifier l'état actuel
        Log::info("État actuel de l'étape ID $id : " . ($etape->is_done ? 'true' : 'false'));
    
        // On inverse la valeur actuelle de is_done
        $etape->is_done = !$etape->is_done;
        $etape->save();
    
        // Log après la mise à jour
        Log::info("Nouveau état de l'étape ID $id : " . ($etape->is_done ? 'true' : 'false'));
    
        return redirect()->back()->with('success', 'L\'état de l\'étape a été mis à jour.');
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
