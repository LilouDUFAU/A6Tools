<?php

namespace App\Http\Controllers;

use App\Models\PrepAtelier;
use App\Models\Commande;
use App\Models\User;
use App\Models\Etape;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PrepAtelierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prepAteliers = PrepAtelier::with(['commande', 'etapes', 'employe'])->get();
        return view('gestatelier.index', compact('prepAteliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Habituellement utilisé pour afficher un formulaire, ici juste les données nécessaires
        $commandes = Commande::all();
        $employes = User::all();

        return view('gestatelier.create', compact('commandes', 'employes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log des données envoyées dans la requête
        Log::debug('Données de la requête:', $request->all());
        
        // Validation des données du formulaire
        $validated = $request->validate([
            'notes' => 'nullable|string', // Modification pour permettre à 'notes' d'être null
            'commande_id' => 'required|exists:commandes,id',  // Validation pour s'assurer que commande_id est correct
            'etapes' => 'required|array', // Assurez-vous que les étapes sont envoyées sous forme de tableau
            'etapes.*' => 'required|string', // Chaque étape doit être une chaîne
            'etapes_done' => 'nullable|array', // Etapes terminées est optionnelle, si envoyée, elle doit être un tableau
            'etapes_done.*' => 'nullable|boolean', // Les valeurs de etapes_done doivent être des booléens
            'employe_id' => 'required|exists:users,id', // Validation de l'ID de l'employé sélectionné
        ]);
        
        // Utiliser l'ID de l'employé sélectionné
        $validated['employe_id'] = $validated['employe_id'];  // L'ID de l'employé sélectionné envoyé dans le formulaire
        
        // Log des données validées
        Log::debug('Données validées:', $validated);
        
        // Création de la préparation de l'atelier
        $prepAtelier = PrepAtelier::create([
            'notes' => $validated['notes'], // 'notes' peut être null ou une chaîne
            'commande_id' => $validated['commande_id'],  // Assure-toi que commande_id est bien passé
            'employe_id' => $validated['employe_id'],    // Utilisation de l'employe_id sélectionné
        ]);
        
        // Log après la création de la préparation
        Log::debug('Préparation de l\'atelier créée:', $prepAtelier->toArray());
        
        // Ajouter les étapes à la préparation de l'atelier
        if (isset($validated['etapes']) && !empty($validated['etapes'])) {
            foreach ($validated['etapes'] as $index => $etape) {
                // Log de chaque étape avant l'insertion
                Log::debug('Création de l\'étape:', [
                    'intitulé' => $etape,
                    'is_done' => isset($validated['etapes_done'][$index]) ? $validated['etapes_done'][$index] : false,
                ]);
        
                // Création de l'étape et association avec la préparation
                Etape::create([
                    'preparation_id' => $prepAtelier->id,  // Associer l'étape à la préparation (utilisation de preparation_id)
                    'intitule' => $etape, // Intitulé de l'étape
                    'is_done' => isset($validated['etapes_done'][$index]) ? $validated['etapes_done'][$index] : false, // Déterminer si l'étape est terminée
                ]);
            }
        }
        
        // Log final pour la redirection
        Log::debug('Préparation et étapes créées avec succès.');
        
        // Retourner vers la liste avec un message de succès
        return redirect()->route('gestatelier.index')->with('success', 'Préparation créée avec succès!');
    }
     
                

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $prepAtelier = PrepAtelier::with(['commande', 'etapes', 'employe'])->findOrFail($id);
        return view('gestatelier.show', compact('prepAtelier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Récupérer l'objet à éditer (ex: une préparation)
        $preparation = PrepAtelier::findOrFail($id);
    
        // Récupérer les commandes et les employés (si nécessaire)
        $commandes = Commande::all();
        $employes = User::all();
    
        // Passer les données à la vue
        return view('gestatelier.edit', compact('preparation', 'commandes', 'employes'));
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) 
    {
        // Validation des données envoyées
        $validated = $request->validate([
            'notes' => 'nullable|string',
            'commande_id' => 'required|exists:commandes,id',
            'employe_id' => 'required|exists:users,id',
            'etapes' => 'required|array',
            'etapes.*' => 'nullable|string', // Chaque étape doit être une chaîne de caractères
            'etapes_done' => 'nullable|array',
            'etapes_done.*' => 'nullable|boolean',
        ]);
    
        // Récupérer la préparation de l'atelier à modifier
        $prepAtelier = PrepAtelier::findOrFail($id);
    
        // Mettre à jour les informations de la préparation
        $prepAtelier->update([
            'notes' => $validated['notes'],
            'commande_id' => $validated['commande_id'],
            'employe_id' => $validated['employe_id'],
        ]);
    
        // Récupérer les étapes envoyées et gérer les ajouts/updates
        if (isset($validated['etapes']) && !empty($validated['etapes'])) {
            // Lister les étapes existantes pour vérifier si elles doivent être mises à jour ou supprimées
            $existingEtapes = $prepAtelier->etapes()->pluck('id')->toArray();
            
            // Parcours des étapes envoyées et mises à jour
            foreach ($validated['etapes'] as $index => $etape) {
                $is_done = isset($validated['etapes_done'][$index]) ? $validated['etapes_done'][$index] : false;
                
                // Vérifier si l'étape existe déjà dans la base de données
                $existingEtapeId = isset($existingEtapes[$index]) ? $existingEtapes[$index] : null;
                
                if ($existingEtapeId) {
                    // Si l'étape existe, vérifier si l'état est déjà à true
                    $etapeRecord = Etape::findOrFail($existingEtapeId);
    
                    // Si l'état actuel est déjà à true, on ne permet pas de le mettre à false
                    if ($etapeRecord->is_done && $is_done === false) {
                        // Si is_done est true, on garde cette valeur sans la modifier
                        $is_done = true;
                    }
    
                    // Mettre à jour l'étape avec le nouvel intitulé et l'état (is_done)
                    $etapeRecord->update([
                        'intitule' => $etape,
                        'is_done' => $is_done,
                    ]);
                } else {
                    // Si l'étape n'existe pas, l'ajouter
                    Etape::create([
                        'preparation_id' => $prepAtelier->id,
                        'intitule' => $etape,
                        'is_done' => $is_done,
                    ]);
                }
            }
    
            // Supprimer les étapes qui n'existent plus dans le formulaire
            $newEtapeIds = $prepAtelier->etapes()->whereIn('intitule', $validated['etapes'])->pluck('id')->toArray();
            $prepAtelier->etapes()->whereNotIn('id', $newEtapeIds)->delete();
        }
    
        // Retourner la préparation mise à jour avec un message de succès
        return redirect()->route('gestatelier.index')->with('success', 'Préparation mise à jour avec succès!');
    }
    
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $prepAtelier = PrepAtelier::findOrFail($id);

        // Supprimer toutes les étapes associées à la préparation
        $prepAtelier->etapes()->delete();

        // Supprimer la préparation elle-même
        $prepAtelier->delete();

        return redirect()->route('gestatelier.index')->with('success', 'Préparation et ses étapes associées supprimées avec succès!');
    }
}
