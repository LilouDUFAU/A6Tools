
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

        // Compte le nombre de "locPret" qui ont UNIQUEMENT ce statut
        // (pas le nombre total de PC, mais le nombre de contrats pour chaque statut)
        $totalLocPretsByStatus = [
            'prêté' => $locPrets->filter(function($locPret) {
                // Vérifie si AU MOINS UN des PC de ce locPret a le statut 'prêté'
                return $locPret->pcrenouvs->where('statut', 'prêté')->count() > 0;
            })->count(),
            'loué' => $locPrets->filter(function($locPret) {
                // Vérifie si AU MOINS UN des PC de ce locPret a le statut 'loué'
                return $locPret->pcrenouvs->where('statut', 'loué')->count() > 0;
            })->count()
        ];

        // Labels personnalisés
        $statutLabels = [
            'prêté' => 'Prêt',
            'loué' => 'Location'
        ];
    @endphp
    @foreach($statutCouleurs as $statut => $classes)
        <div class="filter-btn {{ $classes }} text-white text-center py-6 rounded-lg shadow-md cursor-pointer" data-filter="{{ $statut }}" data-type="statut">
            <div class="text-3xl font-bold count-display" id="count-{{ $statut }}">{{ $totalLocPretsByStatus[$statut] }}</div>
            <div class="text-lg">{{ $statutLabels[$statut] }}</div>
        </div>
    @endforeach
</div>

<div class="flex flex-wrap justify-between items-center mb-4 px-4 gap-4">
    <div class="flex flex-wrap gap-4 w-full sm:w-auto">
        <button id="resetFilters" class="bg-gray-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-gray-700 w-full sm:w-auto">Réinitialiser les filtres</button>
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
        <table class="min-w-full bg-white shadow-md rounded-lg">
            <thead>
                <tr id="table-headers" class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                    <th class="px-6 py-3 border border-gray-200">Client</th>
                    <th class="px-6 py-3 border border-gray-200">Date de début</th>
                    <th class="px-6 py-3 border border-gray-200">Date de retour</th>
                    <th class="px-6 py-3 border border-gray-200">PC</th>
                    <th class="px-6 py-3 border border-gray-200">Statut</th>
                    <th class="px-6 py-3 border border-gray-200">Actions</th>
                </tr>
            </thead>
            <tbody id="locpret-body">
                @forelse($locPrets as $locPret)
                <tr class="border-t hover:bg-gray-50 item-row" data-statut="{{ strtolower($locPret->pcrenouvs->first()?->statut ?? 'inconnu') }}">
                    <td class="px-6 py-3 border border-gray-200">
                        @if(isset($locPret->clients))
                            {{ $locPret->clients->nom }} {{ $locPret->clients->prenom }}
                            <span class="text-gray-500">({{ $locPret->clients->code_client }})</span>
                        @else
                            Client non défini
                        @endif
                    </td>
                    <td class="px-6 py-3 border border-gray-200">{{ $locPret->date_debut }}</td>
                    <td class="px-6 py-3 border border-gray-200">{{ $locPret->date_retour }}</td>
                    <td class="px-6 py-3 border border-gray-200">
                        {{ $locPret->pcrenouvs->count() }} PC(s)
                    </td>
                    <td class="px-6 py-3 border border-gray-200">
                        @php
                            $statut = $locPret->pcrenouvs->first() ? $locPret->pcrenouvs->first()->statut : 'inconnu';
                            $badgeColor = match($statut) {
                                'prêté' => 'bg-green-100 text-green-800',
                                'loué' => 'bg-yellow-100 text-yellow-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeColor }}">
                            {{ ucfirst($statut) }}
                        </span>
                    </td>
                    <td class="px-6 py-3 border border-gray-200 space-x-1">
                        <a href="{{ route('locpret.show', $locPret) }}" class="font-semibold text-green-600 hover:text-green-700">
                            Détails
                        </a>
                        <a href="{{ route('locpret.edit', $locPret) }}" class="font-semibold text-yellow-600 hover:text-yellow-700">
                            Modifier
                        </a>
                        <button type="button" onclick="openModal('returnModal{{ $locPret->id }}')" class="font-semibold text-blue-600 hover:text-blue-700">
                            Retourner
                        </button>
                        <button type="button" onclick="openModal('deleteModal{{ $locPret->id }}')" class="font-semibold text-red-600 hover:text-red-700">
                            Supprimer
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 border border-gray-200 text-center text-gray-500">
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
                            'loué' => 'bg-yellow-100 text-yellow-800',
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
                <a href="{{ route('locpret.show', $locPret) }}" class="font-semibold text-green-600 hover:text-green-700">
                    Détails
                </a>
                <a href="{{ route('locpret.edit', $locPret) }}" class="font-semibold text-yellow-600 hover:text-yellow-700">
                    Modifier
                </a>
                <button type="button" onclick="openModal('returnModal{{ $locPret->id }}')" class="font-semibold text-blue-600 hover:text-blue-700">
                    Retourner
                </button>
                <button type="button" onclick="openModal('deleteModal{{ $locPret->id }}')" class="font-semibold text-red-600 hover:text-red-700">
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
<div id="returnModal{{ $locPret->id }}" class="hidden fixed inset-0 bg-gray-800/40 flex items-center justify-center z-50 modal-overlay">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 modal-content" onclick="event.stopPropagation();">
        <div class="flex justify-between items-center mb-4">
            <h5 class="text-lg font-semibold">Confirmation de retour</h5>
            <button onclick="closeModal('returnModal{{ $locPret->id }}')" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <p class="mb-6">Êtes-vous sûr de vouloir marquer tous les PC de cette opération comme retournés ?</p>
        <div class="flex justify-end space-x-2">
            <button onclick="closeModal('returnModal{{ $locPret->id }}')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Annuler</button>
            <form action="{{ route('locpret.retourner', $locPret) }}" method="POST">
                @csrf
                @method('PUT')
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Confirmer le retour</button>
            </form>
        </div>
    </div>
</div>
<div id="deleteModal{{ $locPret->id }}" class="hidden fixed inset-0 bg-gray-800/40 flex items-center justify-center z-50 modal-overlay">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 modal-content" onclick="event.stopPropagation();">
        <div class="flex justify-between items-center mb-4">
            <h5 class="text-lg font-semibold">Confirmation de suppression</h5>
            <button onclick="closeModal('deleteModal{{ $locPret->id }}')" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <p class="mb-6">Êtes-vous sûr de vouloir supprimer cette opération de prêt/location ? Les PC associés seront remis en stock.</p>
        <div class="flex justify-end space-x-2">
            <button onclick="closeModal('deleteModal{{ $locPret->id }}')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Annuler</button>
            <form action="{{ route('locpret.destroy', $locPret) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Supprimer</button>
            </form>
        </div>
    </div>
</div>
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
    const modalOverlays = document.querySelectorAll('.modal-overlay');

    let activeFilters = {statut: new Set()};
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
            const visible = activeFilters.statut.size === 0 || activeFilters.statut.has(statut);
            item.style.display = visible ? '' : 'none';
        });

        updateTableTitle();
        updateCounts();
    }

    // Met à jour le titre de la table selon filtres
    function updateTableTitle() {
        if (activeFilters.statut.size === 0) {
            tableTitle.textContent = "Liste des Locations et Prêts";
        } else {
            tableTitle.textContent = "Liste des Locations et Prêts - Filtré par : " + Array.from(activeFilters.statut).map(f => f.charAt(0).toUpperCase() + f.slice(1)).join(", ");
        }
    }

    // Met à jour les compteurs dynamiques sous les filtres
    function updateCounts() {
        ['prêté', 'loué'].forEach(statut => {
            let count = 0;
            const items = document.querySelectorAll(`.item-row[data-statut="${statut}"], .item-card[data-statut="${statut}"]`);
            
            // Pour chaque statut, nous comptons combien d'éléments sont visibles
            items.forEach(item => {
                if (item.style.display !== 'none') {
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
            if (activeFilters.statut.has(statut)) {
                activeFilters.statut.delete(statut);
                btn.classList.remove('ring-4', 'ring-blue-500');
            } else {
                activeFilters.statut.add(statut);
                btn.classList.add('ring-4', 'ring-blue-500');
            }
            updateVisibility();
        });
    });

    // Reset filtres
    resetFiltersBtn.addEventListener('click', () => {
        activeFilters.statut = new Set();
        filterButtons.forEach(btn => btn.classList.remove('ring-4', 'ring-blue-500'));
        updateVisibility();
    });

    // Changement de vue
    listViewBtn.addEventListener('click', () => setView('list'));
    gridViewBtn.addEventListener('click', () => setView('grid'));

    // Fermeture des modales en cliquant sur l'overlay
    modalOverlays.forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            // Si le clic est directement sur l'overlay (pas sur son contenu)
            if (e.target === overlay) {
                closeModal(overlay.id);
            }
        });
    });

    // Ajout de gestionnaires d'événements pour la touche Échap
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay:not(.hidden)').forEach(modal => {
                closeModal(modal.id);
            });
        }
    });

    // Initialisation
    setView(currentView);
});

// Fonctions de gestion de modales
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        // Empêcher le défilement de la page en arrière-plan
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        // Restaurer le défilement de la page
        document.body.style.overflow = '';
    }
}
</script>
@endsection