@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-8 px-4 pt-10 text-gray-800">Gestion des Locations et Prêts</h1>

    {{-- Filtres par Statut --}}
    <h2 class="text-2xl font-semibold px-4 py-2 text-gray-700">Filtrer par statut</h2>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8 px-4">
    @php
        $statutCouleurs = [
            'prêté' => 'bg-green-600 hover:bg-green-700',
            'loué' => 'bg-red-600 hover:bg-red-700'
        ];

        // Calculate total PCs for each status
        $totalPCs = [
            'prêté' => $locPrets->flatMap->pcrenouvs->where('statut', 'prêté')->count(),
            'loué' => $locPrets->flatMap->pcrenouvs->where('statut', 'loué')->count()
        ];

        // Labels personnalisés
        $statutLabels = [
            'prêté' => 'Prêt',
            'loué' => 'Location'
        ];
    @endphp
    @foreach($statutCouleurs as $statut => $classes)
        <div class="filter-btn {{ $classes }} text-white text-center py-6 rounded-lg shadow-md cursor-pointer" data-filter="{{ $statut }}" data-type="statut">
            <div class="text-3xl font-bold count-display" id="count-{{ $statut }}">{{ $totalPCs[$statut] }}</div>
            <div class="text-lg">{{ $statutLabels[$statut] }}</div>
        </div>
    @endforeach
</div>

    <div class="flex flex-wrap justify-between items-center mb-6 px-4 gap-4">
        <div class="flex flex-wrap gap-4 w-full sm:w-auto">
            <button id="resetFilters" class="bg-gray-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-gray-700 w-full sm:w-auto flex items-center justify-center">
                Réinitialiser les filtres
            </button>
            <a href="{{ route('gestrenouv.index') }}" class="bg-purple-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-purple-700 w-full sm:w-auto text-center">
                Liste des PC RenouvO
            </a>
            <a href="{{ route('locpret.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700 w-full sm:w-auto text-center">
                Nouveau Loc/Prêt
            </a>
        </div>
        
        <div class="flex items-center gap-2">
            <span class="text-gray-700">Vue:</span>
            <button id="listView" class="p-2 rounded-lg transition-colors bg-blue-600 text-white" aria-label="Vue liste">
                <i class="fas fa-list"></i>
            </button>
            <button id="gridView" class="p-2 rounded-lg transition-colors bg-gray-200 text-gray-700" aria-label="Vue mosaïque">
                <i class="fas fa-th"></i>
            </button>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <h2 id="table-title" class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4">
            Liste des Locations et Prêts
        </h2>

        <div id="listViewContent" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de début</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de retour</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PC</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($locPrets as $locPret)
                    <tr class="hover:bg-gray-50 item-row" data-statut="{{ strtolower($locPret->pcrenouvs->first()?->statut ?? 'inconnu') }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if(isset($locPret->clients))
                                {{ $locPret->clients->nom }} {{ $locPret->clients->prenom }}
                                <span class="text-gray-500">({{ $locPret->clients->code_client }})</span>
                            @else
                                Client non défini
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $locPret->date_debut }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $locPret->date_retour }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $locPret->pcrenouvs->count() }} PC(s)
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @php
                                $statut = $locPret->pcrenouvs->first() ? $locPret->pcrenouvs->first()->statut : 'inconnu';
                                $badgeColor = match($statut) {
                                    'prêté' => 'bg-green-100 text-green-800',
                                    'loué' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeColor }}">
                                {{ ucfirst($statut) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-1">
                            <a href="{{ route('locpret.show', $locPret) }}" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition text-xs">
                                Détails
                            </a>
                            <a href="{{ route('locpret.edit', $locPret) }}" class="px-2 py-1 bg-yellow-400 text-yellow-900 rounded hover:bg-yellow-500 transition text-xs">
                                Modifier
                            </a>
                            <button type="button" onclick="openModal('returnModal{{ $locPret->id }}')" class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition text-xs">
                                Retourner
                            </button>
                            <button type="button" onclick="openModal('deleteModal{{ $locPret->id }}')" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition text-xs">
                                Supprimer
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                            Aucune location ou prêt trouvé
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="gridViewContent" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($locPrets as $locPret)
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow item-card" 
                 data-statut="{{ strtolower($locPret->pcrenouvs->first()?->statut ?? 'inconnu') }}">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                @if(isset($locPret->clients))
                                    {{ $locPret->clients->nom }} {{ $locPret->clients->prenom }}
                                @else
                                    Client non défini
                                @endif
                            </h3>
                            @if(isset($locPret->clients))
                                <p class="text-sm text-gray-500">{{ $locPret->clients->code_client }}</p>
                            @endif
                        </div>
                        @php
                            $statut = $locPret->pcrenouvs->first() ? $locPret->pcrenouvs->first()->statut : 'inconnu';
                            $badgeColor = match($statut) {
                                'prêté' => 'bg-green-100 text-green-800',
                                'loué' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeColor }}">
                            {{ ucfirst($statut) }}
                        </span>
                    </div>
                </div>
                
                <div class="p-4 space-y-3">
                    <div class="flex items-center text-sm">
                        <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                        <span>Date de début : {{ $locPret->date_debut }}</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <i class="fas fa-calendar-check mr-2 text-gray-400"></i>
                        <span>Date de retour : {{ $locPret->date_retour }}</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <i class="fas fa-laptop mr-2 text-gray-400"></i>
                        <span>{{ $locPret->pcrenouvs->count() }} PC(s)</span>
                    </div>
                </div>

                <div class="p-4 border-t border-gray-200 flex justify-between">
                    <a href="{{ route('locpret.show', $locPret) }}" class="px-3 py-1 text-white bg-blue-500 rounded hover:bg-blue-600 transition text-xs">
                        Détails
                    </a>
                    <a href="{{ route('locpret.edit', $locPret) }}" class="px-3 py-1 text-yellow-900 bg-yellow-400 rounded hover:bg-yellow-500 transition text-xs">
                        Modifier
                    </a>
                    <button type="button" onclick="openModal('returnModal{{ $locPret->id }}')" class="px-3 py-1 text-white bg-green-500 rounded hover:bg-green-600 transition text-xs">
                        Retourner
                    </button>
                    <button type="button" onclick="openModal('deleteModal{{ $locPret->id }}')" class="px-3 py-1 text-white bg-red-500 rounded hover:bg-red-600 transition text-xs">
                        Supprimer
                    </button>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center text-gray-500 py-6">
                Aucune location ou prêt trouvé
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modales déplacées en dehors des vues liste/grille pour éviter les doublons et erreurs d'accessibilité -->
@foreach($locPrets as $locPret)
<div id="returnModal{{ $locPret->id }}" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h5 class="text-lg font-semibold">Confirmation de retour</h5>
            <button onclick="toggleModal('returnModal{{ $locPret->id }}')" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <p class="mb-6">Êtes-vous sûr de vouloir marquer tous les PC de cette opération comme retournés ?</p>
        <div class="flex justify-end space-x-2">
            <button onclick="toggleModal('returnModal{{ $locPret->id }}')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Annuler</button>
            <form action="{{ route('locpret.retourner', $locPret) }}" method="POST">
                @csrf
                @method('PUT') <!-- ✅ Corrigé ici -->
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Confirmer le retour</button>
            </form>
        </div>
    </div>
</div>

<div id="deleteModal{{ $locPret->id }}" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h5 class="text-lg font-semibold">Confirmation de suppression</h5>
            <button onclick="toggleModal('deleteModal{{ $locPret->id }}')" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <p class="mb-6">Êtes-vous sûr de vouloir supprimer cette opération de prêt/location ? Les PC associés seront remis en stock.</p>
        <div class="flex justify-end space-x-2">
            <button onclick="toggleModal('deleteModal{{ $locPret->id }}')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Annuler</button>
            <form action="{{ route('locpret.destroy', $locPret) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Supprimer</button>
            </form>
        </div>
    </div></div>
@endforeach

<script>
document.addEventListener('DOMContentLoaded', () => {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const resetFiltersBtn = document.getElementById('resetFilters');
    const listViewBtn = document.getElementById('listView');
    const gridViewBtn = document.getElementById('gridView');
    const listViewContent = document.getElementById('listViewContent');
    const gridViewContent = document.getElementById('gridViewContent');
    const tableTitle = document.getElementById('table-title');

    let activeFilters = new Set();
    let currentView = localStorage.getItem('locpretViewMode') || 'list';

    // Initial setup of view
    function setView(view) {
        currentView = view;
        localStorage.setItem('locpretViewMode', view);

        const isList = view === 'list';
        listViewContent.classList.toggle('hidden', !isList);
        gridViewContent.classList.toggle('hidden', isList);

        listViewBtn.classList.toggle('bg-blue-600', isList);
        listViewBtn.classList.toggle('text-white', isList);
        listViewBtn.classList.toggle('bg-gray-200', !isList);
        listViewBtn.classList.toggle('text-gray-700', !isList);

        gridViewBtn.classList.toggle('bg-blue-600', !isList);
        gridViewBtn.classList.toggle('text-white', !isList);
        gridViewBtn.classList.toggle('bg-gray-200', isList);
        gridViewBtn.classList.toggle('text-gray-700', isList);

        updateVisibility();
    }

    // Met à jour la visibilité des éléments selon les filtres
    function updateVisibility() {
        const items = currentView === 'list' 
            ? document.querySelectorAll('.item-row')
            : document.querySelectorAll('.item-card');

        items.forEach(item => {
            const statut = item.dataset.statut.toLowerCase();
            const visible = activeFilters.size === 0 || activeFilters.has(statut);
            item.style.display = visible ? '' : 'none';
        });

        updateTableTitle();
        updateCounts();
    }

    // Met à jour le titre de la table selon filtres
    function updateTableTitle() {
        if (activeFilters.size === 0) {
            tableTitle.textContent = "Liste des Locations et Prêts";
        } else {
            tableTitle.textContent = "Liste des Locations et Prêts - Filtré par : " + Array.from(activeFilters).map(f => f.charAt(0).toUpperCase() + f.slice(1)).join(", ");
        }
    }

    // Met à jour les compteurs dynamiques sous les filtres
    function updateCounts() {
        ['prêté', 'loué'].forEach(statut => {
            let count = 0;

            const items = currentView === 'list' 
                ? document.querySelectorAll('.item-row')
                : document.querySelectorAll('.item-card');

            items.forEach(item => {
                const itemStatut = item.dataset.statut.toLowerCase();
                // Compter uniquement les éléments visibles ET qui correspondent au statut
                if ((item.style.display === '' || item.style.display === 'table-row' || item.style.display === 'block') && itemStatut === statut) {
                    count++;
                }
            });

            const countElem = document.getElementById(`count-${statut}`);
            if(countElem) {
                countElem.textContent = count;
            }
        });
    }

    // Gestion du clic sur un filtre
    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const statut = btn.dataset.filter.toLowerCase();
            if (activeFilters.has(statut)) {
                activeFilters.delete(statut);
                btn.classList.remove('ring-4', 'ring-blue-500');
            } else {
                activeFilters.add(statut);
                btn.classList.add('ring-4', 'ring-blue-500');
            }
            updateVisibility();
        });
    });

    // Reset filtres
    resetFiltersBtn.addEventListener('click', () => {
        activeFilters.clear();
        filterButtons.forEach(btn => btn.classList.remove('ring-4', 'ring-blue-500'));
        updateVisibility();
    });

    // Changement de vue
    listViewBtn.addEventListener('click', () => setView('list'));
    gridViewBtn.addEventListener('click', () => setView('grid'));

    // Initialisation
    // On applique le style ring sur filtres actifs au chargement (aucun par défaut)
    filterButtons.forEach(btn => {
        const statut = btn.dataset.filter.toLowerCase();
        if (activeFilters.has(statut)) {
            btn.classList.add('ring-4', 'ring-blue-500');
        }
    });

    setView(currentView);
    
    // Ajout de gestionnaires d'événements pour fermer les modales lors d'un clic à l'extérieur
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal-container')) {
            closeModal(e.target.id);
        }
    });
    
    // Ajout de gestionnaires d'événements pour la touche Échap
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-container:not(.hidden)').forEach(modal => {
                closeModal(modal.id);
            });
        }
    });
});

// Amélioration des fonctions de gestion de modales
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        // Empêcher le défilement de la page en arrière-plan
        document.body.style.overflow = 'hidden';
        // Utiliser flex pour centrer la modale
        modal.style.display = 'flex';
    } else {
        console.warn(`Modal avec l'ID "${modalId}" introuvable.`);
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        // Restaurer le défilement de la page
        document.body.style.overflow = '';
        // Réinitialiser le style d'affichage
        modal.style.display = 'none';
    } else {
        console.warn(`Modal avec l'ID "${modalId}" introuvable.`);
    }
}
</script>
@endsection