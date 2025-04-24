@extends('layouts.app')

@section('content')

<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8 min-h-screen">
    <h1 class="text-3xl font-bold mb-8 px-4 pt-10 text-gray-800">Gestion des Préparations Atelier</h1>


    <div class="flex justify-end items-center mb-4 px-4">
        <a href="{{ route('prepatelier.create') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-green-700">
            Ajouter une préparation
        </a>
    </div>

    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
    <h2 class="text-xl sm:text-2xl font-semibold px-2 sm:px-4 py-2 text-gray-700">Liste des Préparations Atelier</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                        <th class="py-3 px-4 border border-gray-200">Commande client liée</th>
                        <th class="py-3 px-4 border border-gray-200">Notes Technicien</th>
                        <th class="py-3 px-4 border border-gray-200">Etapes terminées</th>
                        <th class="py-3 px-4 border border-gray-200">Etapes restantes</th>
                        <th class="py-3 px-4 border border-gray-200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prepAteliers as $atelier)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="py-3 px-4 border border-gray-200">Cmde n°{{ $atelier->commande->id }} - {{ $atelier->commande->client->nom }} ({{ $atelier->commande->client->code_client }})</td>
                            <td class="py-3 px-4 border border-gray-200 text-center">
                                @if($atelier->notes)
                                    {{ $atelier->notes }}
                                @else
                                    <strong>-</strong>
                                @endif
                            </td>

                            <td class="py-3 px-4 border border-gray-200">
                                {{ $atelier->etapes->where('is_done', true)->count() }}
                            </td>
                            <td class="py-3 px-4 border border-gray-200">
                                {{ $atelier->etapes->where('is_done', false)->count() }}
                            </td>
                            <td class="py-3 px-4 border border-gray-200">
                                <div class="inline-flex space-x-2">
                                    <a href="{{ route('prepatelier.show', $atelier->id) }}" class="text-green-600 font-semibold hover:underline">Détails</a>
                                    <a href="{{ route('prepatelier.edit', $atelier->id) }}" class="text-yellow-600 font-semibold hover:underline">Modifier</a>
                                    <form action="{{ route('prepatelier.destroy', $atelier->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 font-semibold hover:underline" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette préparation atelier ?')">Supprimer</button>
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