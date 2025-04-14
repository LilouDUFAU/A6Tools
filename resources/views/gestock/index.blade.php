@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 py-6 min-h-screen">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <h1 class="text-xl sm:text-2xl font-bold">Liste des Commandes</h1>
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
                    <th class="py-2 sm:py-3 px-2 sm:px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($commandes as $commande)
                <tr class="border-t hover:bg-gray-50">
                    <td class="py-2 sm:py-3 px-2 sm:px-4">{{ $commande->id }}</td>
                    <td class="py-2 sm:py-3 px-2 sm:px-4">{{ $commande->intitule }}</td>
                    <td class="py-2 sm:py-3 px-2 sm:px-4">{{ $commande->client?->nom }}</td>
                    <td class="py-2 sm:py-3 px-2 sm:px-4">{{ $commande->prix_total }} €</td>
                    <td class="py-2 sm:py-3 px-2 sm:px-4">{{ $commande->etat }}</td>
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
