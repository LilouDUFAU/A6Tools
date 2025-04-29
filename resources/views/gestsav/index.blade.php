@extends('layouts.app')

@section('content')

<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8 min-h-screen">
    <h1 class="text-3xl font-bold mb-8 px-4 pt-10 text-gray-800">Gestion des Pannes SAV</h1>
    
    <!-- ajouter les filtres -->
    
    <div class="flex justify-end items-center mb-4 px-4">
        <a href="{{ route('panne.create') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-green-700">
            Ajouter une panne
        </a>
    </div>

    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <h2 class="text-xl sm:text-2xl font-semibold px-2 sm:px-4 py-2 text-gray-700">Liste des Pannes</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                        <th class="py-3 px-4 border border-gray-200">Client</th>
                        <th class="py-3 px-4 border border-gray-200">Fournisseur</th>
                        <th class="py-3 px-4 border border-gray-200">Etat client</th>
                        <th class="py-3 px-4 border border-gray-200">Catégorie panne</th>
                        <th class="py-3 px-4 border border-gray-200">Date panne</th>
                        <th class="py-3 px-4 border border-gray-200">Dernière action</th>
                        <th class="py-3 px-4 border border-gray-200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pannes as $panne)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="py-3 px-4 border border-gray-200">{{ $panne->clients->first()->nom ?? 'N/A' }}</td>
                            <td class="py-3 px-4 border border-gray-200">{{ $panne->fournisseur->nom ?? 'N/A' }}</td>
                            <td class="py-3 px-4 border border-gray-200">{{ $panne->etat_client }}</td>
                            <td class="py-3 px-4 border border-gray-200">{{ $panne->categorie_panne }}</td>
                            <td class="py-3 px-4 border border-gray-200">{{ $panne->date_panne }}</td>
                            <td class="py-3 px-4 border border-gray-200">
                                {{ $panne->actions->last()->intitule ?? 'Aucune action' }} 
                                ({{ $panne->actions->last()->statut ?? 'N/A' }})
                            </td>
                            <td class="py-3 px-4 border border-gray-200">
                                    <div class="inline-flex space-x-2">
                                        <a href="{{ route('panne.show', $panne->id) }}" class="text-green-600 font-semibold hover:underline">Détails</a>
                                        <a href="{{ route('panne.edit', $panne->id) }}" class="text-yellow-600 font-semibold hover:underline">Modifier</a>
                                        <form action="{{ route('panne.destroy', $panne->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 font-semibold hover:underline" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette panne ?')">Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection