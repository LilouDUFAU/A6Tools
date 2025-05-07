@extends('layouts.app')

@section('content')

<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8 min-h-screen">
    <h1 class="text-3xl font-bold mb-8 px-4 pt-10 text-gray-800 text-center sm:text-left">Gestion des Préparations Atelier</h1>

    <div class="flex flex-col sm:flex-row justify-end items-center mb-4 px-4">
        <a href="{{ route('prepatelier.create') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-green-700 text-center">
            Ajouter une préparation
        </a>
    </div>

    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4 text-center sm:text-left">Liste des Préparations Atelier</h2>
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
                    @php
                        $etapesRestantes = $atelier->etapes->where('is_done', false)->count();
                        $cellClass = $etapesRestantes === 0 ? 'bg-green-600/20 ' : '';
                    @endphp
                        <tr class="border-t hover:bg-gray-50">
                            <td class="py-3 px-4 border border-gray-200 {{ $cellClass }}">
                                Cmde n°{{ $atelier->commande->id }} - 
                                @if($atelier->commande->client)
                                    {{ $atelier->commande->client->nom }} ({{ $atelier->commande->client->code_client }})
                                @else
                                    <em>Client non associé</em>
                                @endif
                            </td>
                            <td class="py-3 px-4 border border-gray-200 text-center {{ $cellClass }}">
                                @if($atelier->notes)
                                    {{ $atelier->notes }}
                                @else
                                    <strong>-</strong>
                                @endif
                            </td>
                            <td class="py-3 px-4 border border-gray-200 {{ $cellClass }}">
                                {{ $atelier->etapes->where('is_done', true)->count() }}
                            </td>
                            <td class="py-3 px-4 border border-gray-200 {{ $cellClass }}">
                                {{ $etapesRestantes }}
                            </td>
                            <td class="py-3 px-4 border border-gray-200 {{ $cellClass }}">
                                <div class="inline-flex flex-wrap space-x-2">
                                    <a href="{{ route('prepatelier.show', $atelier->id) }}" class="text-green-600 font-semibold hover:underline">Détails</a>
                                    <a href="{{ route('prepatelier.edit', $atelier->id) }}" class="text-yellow-600 font-semibold hover:underline">Modifier</a>
                                    <form action="{{ route('prepatelier.destroy', $atelier->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="openModal({{ $atelier->id }})" class="text-red-600 hover:text-red-700 font-semibold">Supprimer</button>
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

<div id="modal" class="fixed inset-0 z-50 hidden bg-gray-800/40 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg w-11/12 sm:w-1/2 lg:w-1/3">
        <div class="px-4 py-2 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Confirmation de Suppression</h3>
            <button id="closeModal" class="text-gray-600 hover:text-gray-800">&times;</button>
        </div>
        <div class="p-4">
            <p class="text-gray-700">Êtes-vous sûr de vouloir supprimer cette préparation ? Cette action est irréversible.</p>
        </div>
        <div class="px-4 py-2 flex justify-end space-x-4">
            <button id="cancelModal" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Annuler</button>
            <form id="deleteForm" method="POST" action="{{ route('prepatelier.destroy', 0) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Supprimer</button>
            </form>
        </div>
    </div>
</div>


<script>
    const modal = document.getElementById('modal');
    const closeModal = document.getElementById('closeModal');
    const cancelModal = document.getElementById('cancelModal');
    const deleteForm = document.getElementById('deleteForm');
    const deleteRouteTemplate = "{{ route('prepatelier.destroy', ':id') }}";

    function openModal(id) {
        deleteForm.action = deleteRouteTemplate.replace(':id', id);
        modal.classList.remove('hidden');
    }

    function closeModalHandler() {
        modal.classList.add('hidden');
    }

    closeModal.addEventListener('click', closeModalHandler);
    cancelModal.addEventListener('click', closeModalHandler);
    window.addEventListener('click', (e) => {
        if (e.target === modal) closeModalHandler();
    });
</script>

@endsection
