<?php
/**
 * @file ClientController.php
 * @brief Fichier de déclaration et définition de la classe ClientController
 * @autor Lilou DUFAU
 * @date 2025-04-18
 * @version 1.0
 */

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

/**
 * @brief Classe ClientController
 * @details Cette classe gère les opérations CRUD pour le modèle Client.
 */
class ClientController extends Controller
{
    /**
     * @brief Afficher la liste des clients.
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupérer tous les clients de la base de données
        $clients = Client::all();

        // Retourner la vue avec la liste des clients
        return view('clients.index', compact('clients'));
    }

    /**
     * @brief Afficher le formulaire de création d'un client.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Retourner la vue du formulaire de création de client
        return view('clients.create');
    }

    /**
     * @brief Enregistrer un nouveau client.
     * @param Request $request : La requête HTTP contenant les données du client.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Valider les données reçues du formulaire
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code_client' => 'required|string|max:255|unique:clients,code_client',
            'numero_telephone' => 'nullable|string|max:14',
        ]);

        // Créer un nouveau client avec les données validées
        Client::create($validated);

        // Rediriger vers la liste des clients avec un message de succès
        return redirect()->route('clients.index')->with('success', 'Client créé avec succès.');
    }

    /**
     * @brief Afficher les détails d'un client.
     * @param string $id : L'identifiant du client.
     * @return \Illuminate\View\View
     * @throws ModelNotFoundException
     */
    public function show(string $id)
    {
        // Récupérer le client ou échouer si non trouvé
        $client = Client::findOrFail($id);

        // Retourner la vue des détails du client
        return view('clients.show', compact('client'));
    }

    /**
     * @brief Afficher le formulaire d'édition d'un client.
     * @param string $id : L'identifiant du client.
     * @return \Illuminate\View\View
     * @throws ModelNotFoundException
     */
    public function edit(string $id)
    {
        // Récupérer le client ou échouer si non trouvé
        $client = Client::findOrFail($id);

        // Retourner la vue d'édition avec les données du client
        return view('clients.edit', compact('client'));
    }

    /**
     * @brief Mettre à jour un client existant.
     * @param Request $request : La requête HTTP contenant les données du client.
     * @param string $id : L'identifiant du client.
     * @return \Illuminate\Http\RedirectResponse
     * @throws ModelNotFoundException
     * @throws ValidationException
     * @throws QueryException
     * @throws Exception
     */
    public function update(Request $request, string $id)
    {
        // Récupérer le client ou échouer si non trouvé
        $client = Client::findOrFail($id);

        // Valider les données, en excluant le client courant de la vérification d’unicité
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code_client' => 'required|string|max:255|unique:clients,code_client,' . $client->id,
            'numero_telephone' => 'nullable|string|max:14',
        ]);

        // Mettre à jour le client avec les nouvelles données
        $client->update($validated);

        // Rediriger vers la liste des clients avec un message de succès
        return redirect()->route('clients.index')->with('success', 'Client mis à jour avec succès.');
    }

    /**
     * @brief Supprimer un client.
     * @param string $id : L'identifiant du client.
     * @return \Illuminate\Http\RedirectResponse
     * @throws ModelNotFoundException
     * @throws QueryException
     * @throws Exception
     */
    public function destroy(string $id)
    {
        // Récupérer le client ou échouer si non trouvé
        $client = Client::findOrFail($id);

        // Supprimer le client de la base de données
        $client->delete();

        // Rediriger vers la liste avec un message de confirmation
        return redirect()->route('clients.index')->with('success', 'Client supprimé avec succès.');
    }
}
