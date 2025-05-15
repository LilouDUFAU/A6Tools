@extends('layouts.app')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold">Gestion des Locations et Prêts</h1>
    <a href="{{ route('locpret.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Nouveau Prêt/Location</a>
</div>

<div class="bg-white shadow rounded-lg">
    <div class="overflow-x-auto">
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
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if(isset($locPret->clients))
                            {{ $locPret->clients->nom }} {{ $locPret->clients->prenom }}
                        @else
                            Client non défini
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $locPret->date_debut }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $locPret->date_retour }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($locPret->pcrenouvs->count() > 0)
                            {{ $locPret->pcrenouvs->count() }} PC(s)
                        @else
                            Aucun PC
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @php
                            $statut = $locPret->pcrenouvs->first() ? $locPret->pcrenouvs->first()->statut : 'inconnu';
                            $badgeColor = match($statut) {
                                'prêté' => 'bg-yellow-400 text-yellow-900',
                                'loué' => 'bg-blue-400 text-blue-900',
                                default => 'bg-gray-300 text-gray-700',
                            };
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeColor }}">
                            {{ $statut }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-1">
                        <a href="{{ route('locpret.show', $locPret) }}" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition text-xs">Détails</a>
                        <a href="{{ route('locpret.edit', $locPret) }}" class="px-2 py-1 bg-yellow-400 text-yellow-900 rounded hover:bg-yellow-500 transition text-xs">Modifier</a>
                        <button type="button" data-modal-target="returnModal{{ $locPret->id }}" class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition text-xs" onclick="toggleModal('returnModal{{ $locPret->id }}')">
                            Retourner
                        </button>
                        <button type="button" data-modal-target="deleteModal{{ $locPret->id }}" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition text-xs" onclick="toggleModal('deleteModal{{ $locPret->id }}')">
                            Supprimer
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">Aucune location ou prêt enregistré</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<a href=" {{route('gestrenouv.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Retour vers les PC RenouvO</a>

<!-- Modals -->
@foreach($locPrets as $locPret)
<!-- Retour Modal -->
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


<!-- Delete Modal -->
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
    function toggleModal(id) {
        const modal = document.getElementById(id);
        if(modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }
</script>
@endsection
