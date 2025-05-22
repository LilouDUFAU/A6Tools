@extends('layouts.app')

@section('content')

<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
<h1 class="text-3xl font-bold mb-8 px-4 pt-10 text-gray-800">Tableau de Bord des Pannes SAV</h1>

    {{-- Filtres --}}
    <h2 class="text-2xl font-semibold px-4 py-2 text-gray-700">Répartition des pannes selon l'état client</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8 px-4">
        @php
            $etats = ['Ordi de prêt' => 'bg-green-600 hover:bg-green-700',
                     'Échangé' => 'bg-yellow-600 hover:bg-yellow-700',
                     'En attente' => 'bg-red-600 hover:bg-red-700'];
        @endphp
        @foreach($etats as $etat => $classes)
        <div class="filter-btn {{ $classes }} text-white text-center py-6 rounded-lg shadow-md cursor-pointer" data-filter="{{ strtolower($etat) }}" data-type="etat">
            <div class="text-3xl font-bold count-display">{{ $pannes->where('etat_client', $etat)->count() }}</div>
            <div class="text-lg">{{ $etat }}</div>
        </div>
        @endforeach
    </div>

    <h2 class="text-2xl font-semibold px-4 py-2 text-gray-700">Répartition des pannes selon le statut</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8 px-4">
        @php
            $statuts = [
                'En attente' => 'bg-green-600 hover:bg-green-700',
                'Remboursement' => 'bg-yellow-600 hover:bg-yellow-700',
                'Transit' => 'bg-amber-600 hover:bg-amber-700',
                'Envoyé' => 'bg-orange-600 hover:bg-orange-700',
                'Échange anticipé' => 'bg-red-600 hover:bg-red-700',
            ];
        @endphp
        @foreach($statuts as $statut => $classes)
        <div class="filter-btn {{ $classes }} text-white text-center py-6 rounded-lg shadow-md cursor-pointer" data-filter="{{ strtolower($statut) }}" data-type="statut">
            <div class="text-3xl font-bold count-display">{{ $pannes->where('statut', $statut)->count() }}</div>
            <div class="text-lg">{{ $statut }}</div>
        </div>
        @endforeach
    </div>


    <div class="flex flex-col sm:flex-row justify-between items-center mb-4 px-4 space-y-4 sm:space-y-0 sm:space-x-4">
        <button id="resetFilters" class="w-full sm:w-auto bg-gray-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-gray-700">
            Réinitialiser les filtres
        </button>
        <a href="{{ route('gestsav.create') }}" class="w-full sm:w-auto bg-green-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-green-700 text-center">
            Ajouter une panne
        </a>
    </div>

    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4">Liste des Pannes SAV</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                        <th class="py-3 px-4 border border-gray-200">N° SAV</th>
                        <th class="py-3 px-4 border border-gray-200">Client</th>
                        <th class="py-3 px-4 border border-gray-200">Fournisseur</th>
                        <th class="py-3 px-4 border border-gray-200">Etat client</th>
                        <th class="py-3 px-4 border border-gray-200">Demande</th>
                        <th class="py-3 px-4 border border-gray-200">Statut</th>
                        <th class="py-3 px-4 border border-gray-200">Catégorie panne</th>
                        <th class="py-3 px-4 border border-gray-200">Date panne</th>
                        <th class="py-3 px-4 border border-gray-200">Actions</th>
                    </tr>
                </thead>
                <tbody id="pannes-body">
                    @foreach($pannes as $panne)
                        <tr class="border-t hover:bg-gray-50" data-etat="{{ strtolower($panne->etat_client) }}" data-statut="{{ strtolower($panne->statut) }}">
                        <td class="py-3 px-4 border border-gray-200 text-center relative group">
                            <div class="editable-sav" 
                                data-id="{{ $panne->id }}" 
                                data-value="{{ $panne->numero_sav ?? '-' }}">
                                <div class="display-value font-bold cursor-pointer p-1 rounded transition-colors duration-150 group-hover:bg-blue-50 flex items-center justify-center">
                                    {{ $panne->numero_sav ?? '-' }}
                                </div>
                                <div class="edit-container hidden">
                                    <input type="text" 
                                        class="w-full p-1 border border-blue-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        value="{{ $panne->numero_sav ?? '-' }}"
                                    >
                                    <div class="error-message hidden absolute -bottom-5 left-0 right-0 text-red-500 text-xs bg-white p-1 shadow-sm"></div>
                                </div>
                            </div>
                        </td>
                            <td class="py-3 px-4 border border-gray-200">{{ $panne->clients->first()->nom ?? 'N/A' }}</td>
                            <td class="py-3 px-4 border border-gray-200">{{ $panne->fournisseur->nom ?? 'N/A' }}</td>
                            <td class="py-3 px-4 border border-gray-200">{{ $panne->etat_client ?? 'N/A' }}</td>
                            <td class="py-3 px-4 border border-gray-200">{{ $panne->demande ?? 'N/A' }}</td>
                            <td class="py-3 px-4 border border-gray-200">{{ $panne->statut ?? 'N/A' }}</td>
                            <td class="py-3 px-4 border border-gray-200">{{ $panne->categorie_panne ?? 'N/A' }}</td>
                            <td class="py-3 px-4 border border-gray-200">{{ $panne->date_panne ?? 'N/A' }}</td>
                            <td class="py-3 px-4 border border-gray-200">
                                <div class="inline-flex space-x-2">
                                    <a href="{{ route('gestsav.show', $panne->id) }}" class="text-green-600 font-semibold hover:underline">Détails</a>
                                    <a href="{{ route('gestsav.edit', $panne->id) }}" class="text-yellow-600 font-semibold hover:underline">Modifier</a>
                                    <form action="{{ route('gestsav.destroy', $panne->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="openModal({{ $panne->id }})" class="text-red-600 hover:text-red-700 font-semibold">Supprimer</button> 
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
            <p class="text-gray-700">Êtes-vous sûr de vouloir supprimer cette panne SAV ? Cette action est irréversible.</p>
        </div>
        <div class="px-4 py-2 flex justify-end space-x-4">
            <button id="cancelModal" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Annuler</button>
            <form id="deleteForm" method="POST" action="{{ route('gestsav.destroy', 0) }}">
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
    const deleteRouteTemplate = "{{ route('gestsav.destroy', ':id') }}";

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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const activeFilters = {
            etat: new Set(),
            statut: new Set()
        };

        const pannesBody = document.getElementById('pannes-body');
        const allRows = pannesBody.querySelectorAll('tr');

        function updateTable() {
            allRows.forEach(row => {
                const etat = row.dataset.etat;
                const statut = row.dataset.statut;

                const matchEtat = activeFilters.etat.size === 0 || activeFilters.etat.has(etat);
                const matchStatut = activeFilters.statut.size === 0 || activeFilters.statut.has(statut);

                const shouldShow = matchEtat && matchStatut;
                row.style.display = shouldShow ? '' : 'none';
            });

            // Mise à jour des compteurs (optionnel)
            document.querySelectorAll('.filter-btn').forEach(btn => {
                const type = btn.dataset.type;
                const value = btn.dataset.filter;

                const count = Array.from(allRows).filter(row => {
                    const rowValue = row.dataset[type];
                    const rowEtat = row.dataset.etat;
                    const rowStatut = row.dataset.statut;

                    const matchEtat = activeFilters.etat.size === 0 || activeFilters.etat.has(rowEtat);
                    const matchStatut = activeFilters.statut.size === 0 || activeFilters.statut.has(rowStatut);

                    return rowValue === value && matchEtat && matchStatut;
                }).length;

                btn.querySelector('.count-display').textContent = count;
            });
        }

        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const type = btn.dataset.type;
                const value = btn.dataset.filter;

                if (activeFilters[type].has(value)) {
                    activeFilters[type].delete(value);
                    btn.classList.remove('ring-4', 'ring-blue-500');
                } else {
                    activeFilters[type].add(value);
                    btn.classList.add('ring-4', 'ring-blue-500');
                }

                updateTable();
            });
        });

        document.getElementById('resetFilters').addEventListener('click', () => {
            activeFilters.etat.clear();
            activeFilters.statut.clear();
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('ring-4', 'ring-blue-500');
            });
            updateTable();
        });

        updateTable();
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.editable-sav').forEach(container => {
        const displayValue = container.querySelector('.display-value');
        const editContainer = container.querySelector('.edit-container');
        const input = editContainer.querySelector('input');
        const errorMessage = container.querySelector('.error-message');
        
        let isEditing = false;
        let isSaving = false;
        
        function showError(message) {
            errorMessage.textContent = message;
            errorMessage.classList.remove('hidden');
        }
        
        function hideError() {
            errorMessage.classList.add('hidden');
        }
        
        function startEditing() {
            if (isEditing || isSaving) return;
            
            isEditing = true;
            displayValue.classList.add('hidden');
            editContainer.classList.remove('hidden');
            input.focus();
            input.select();
        }
        
        function stopEditing() {
            isEditing = false;
            displayValue.classList.remove('hidden');
            editContainer.classList.add('hidden');
            hideError();
        }
        
        async function saveChanges() {
            if (isSaving) return;
            
            const newValue = input.value.trim();
            if (newValue === '') {
                showError('Le numéro SAV ne peut pas être vide');
                return;
            }
            
            isSaving = true;
            try {
                const response = await fetch(`/gestsav/${container.dataset.id}/update-sav`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ numero_sav: newValue })
                });
                
                if (!response.ok) throw new Error('Échec de la mise à jour');
                
                displayValue.textContent = newValue;
                stopEditing();
            } catch (error) {
                showError('Une erreur est survenue');
            } finally {
                isSaving = false;
            }
        }
        
        displayValue.addEventListener('click', startEditing);
        
        input.addEventListener('blur', () => {
            if (!isSaving) {
                if (input.value === container.dataset.value) {
                    stopEditing();
                } else {
                    saveChanges();
                }
            }
        });
        
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveChanges();
            } else if (e.key === 'Escape') {
                input.value = container.dataset.value;
                stopEditing();
            }
        });
    });
});
</script>


@endsection
