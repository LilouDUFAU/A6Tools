@extends('layouts.app')

@section('content')

<h1 class="text-3xl font-bold mb-6 px-2 pt-10">Tableau de Bord des Commandes</h1>

<h2 class="text-xl sm:text-2xl font-bold px-2 py-1">Nombre de commandes par état</h2>
<div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6 px-2 py-1">
    <div class="bg-green-600 text-white text-center py-4 rounded shadow-lg hover:bg-green-700">
        <div class="text-2xl font-bold">{{ $commandes->where('etat', 'terminée')->count() }}</div>
        <div>Terminée(s)</div>
    </div>
    <div class="bg-yellow-600 text-white text-center py-4 rounded shadow-lg hover:bg-yellow-700">
        <div class="text-2xl font-bold">{{ $commandes->where('etat', 'en_cours')->count() }}</div>
        <div>En cours</div>
    </div>
    <div class="bg-orange-600 text-white text-center py-4 rounded shadow-lg hover:bg-orange-700">
        <div class="text-2xl font-bold">{{ $commandes->where('etat', 'en_attente')->count() }}</div>
        <div>En attente</div>
    </div>
    <div class="bg-red-600 text-white text-center py-4 rounded shadow-lg hover:bg-red-700">
        <div class="text-2xl font-bold">{{ $commandes->where('etat', 'annulé')->count() }}</div>
        <div>Annulée(s)</div>
    </div>
</div>

<h2 class="text-xl sm:text-2xl font-bold px-2 py-1">Nombre de commandes par urgence</h2>
<div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6 px-2 py-1">
    <div class="bg-green-600 text-white text-center py-4 rounded shadow-lg hover:bg-green-700">
        <div class="text-2xl font-bold">{{ $commandes->where('urgence', 'peu urgent')->count() }}</div>
        <div>Peu urgente(s)</div>
    </div>
    <div class="bg-yellow-600 text-white text-center py-4 rounded shadow-lg hover:bg-yellow-700">
        <div class="text-2xl font-bold">{{ $commandes->where('urgence', 'moyennement urgent')->count() }}</div>
        <div>Moyennement urgente(s)</div>
    </div>
    <div class="bg-orange-600 text-white text-center py-4 rounded shadow-lg hover:bg-orange-700">
        <div class="text-2xl font-bold">{{ $commandes->where('urgence', 'urgent')->count() }}</div>
        <div>Urgente(s)</div>
    </div>
    <div class="bg-red-600 text-white text-center py-4 rounded shadow-lg hover:bg-red-700">
        <div class="text-2xl font-bold">{{ $commandes->where('urgence', 'très urgent')->count() }}</div>
        <div>Très urgente(s)</div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 py-6 min-h-screen">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <h2 class="text-xl sm:text-2xl font-bold">Liste des Commandes</h2>
        <a href="{{ route('commande.create') }}" class="mt-4 md:mt-0 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 font-semibold">Nouvelle commande</a>
    </div>

    @if (session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white shadow rounded">
            <thead>
                <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-600">
                    <th class="py-2 sm:py-3 px-2 sm:px-4">#</th>
                    <th class="py-2 sm:py-3 px-2 sm:px-4">Intitulé</th>
                    <th class="py-2 sm:py-3 px-2 sm:px-4">Client</th>
                    <th class="py-2 sm:py-3 px-2 sm:px-4">Prix Total</th>
                    <th class="py-2 sm:py-3 px-2 sm:px-4">État</th>
                    <th class="py-2 sm:py-3 px-2 sm:px-4">Urgence</th>
                    <th class="py-2 sm:py-3 px-2 sm:px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($commandes as $commande)
                <tr class="border-t hover:bg-gray-50">
                    <td class="py-2 sm:py-3 px-2 sm:px-4">{{ $commande->id }}</td>
                    <td class="py-2 sm:py-3 px-2 sm:px-4">{{ $commande->intitule }}</td>
                    <td class="py-2 sm:py-3 px-2 sm:px-4 text-center">{{ $commande->client ? $commande->client->nom : '/' }}</td>
                    <td class="py-2 sm:py-3 px-2 sm:px-4">{{ $commande->prix_total }} €</td>
                    <td class="py-2 sm:py-3 px-2 sm:px-4">{{ $commande->etat }}</td>
                    <td class="py-2 sm:py-3 px-2 sm:px-4">
                        <span class="
                            @if($commande->urgence === 'pas urgent') text-green-800 
                            @elseif($commande->urgence === 'peu urgent') text-green-500 
                            @elseif($commande->urgence === 'moyennement urgent') text-yellow-500 
                            @elseif($commande->urgence === 'urgent') text-orange-500 
                            @elseif($commande->urgence === 'très urgent') text-red-500 
                            @endif
                            font-semibold">
                            {{ $commande->urgence }}
                        </span>
                    </td>
                    <td class="py-2 sm:py-3 px-2 sm:px-4 space-x-2">
                        <a href="{{ route('commande.show', $commande->id) }}" class="text-green-600 hover:underline">Voir</a>
                        <a href="{{ route('commande.edit', $commande->id) }}" class="text-yellow-600 hover:underline">Modifier</a>
                        <form action="{{ route('commande.destroy', $commande->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Confirmer la suppression ?')" class="text-red-600 hover:underline">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
