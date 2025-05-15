@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow rounded-lg">
        <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
            <h3 class="text-xl font-semibold text-gray-900">Détails de la Location/Prêt</h3>
            <div class="space-x-2">
                <a href="{{ route('locpret.index') }}" class="inline-block px-3 py-1 text-sm bg-gray-500 text-white rounded hover:bg-gray-600 transition">Retour</a>
                <a href="{{ route('locpret.edit', $locPret->id) }}" class="inline-block px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition">Modifier</a>
                <button type="button" onclick="toggleModal('deleteModal')" class="inline-block px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700 transition">Supprimer</button>
                <button type="button" onclick="toggleModal('returnModal')" class="inline-block px-3 py-1 text-sm bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">Retourner tous les PC</button>
            </div>
        </div>
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div>
                    <h4 class="text-lg font-semibold mb-4">Informations générales</h4>
                    <table class="w-full table-auto border-collapse border border-gray-300">
                        <tbody>
                            <tr class="border border-gray-300">
                                <th class="border border-gray-300 px-4 py-2 text-left bg-gray-50">Client</th>
                                <td class="border border-gray-300 px-4 py-2">{{ $locPret->clients->nom }} {{ $locPret->clients->prenom }}</td>
                            </tr>
                            <tr class="border border-gray-300">
                                <th class="border border-gray-300 px-4 py-2 text-left bg-gray-50">Date de début</th>
                                <td class="border border-gray-300 px-4 py-2">{{ \Carbon\Carbon::parse($locPret->date_debut)->format('d/m/Y') }}</td>
                            </tr>
                            <tr class="border border-gray-300">
                                <th class="border border-gray-300 px-4 py-2 text-left bg-gray-50">Date de retour prévue</th>
                                <td class="border border-gray-300 px-4 py-2">{{ \Carbon\Carbon::parse($locPret->date_retour)->format('d/m/Y') }}</td>
                            </tr>
                            <tr class="border border-gray-300">
                                <th class="border border-gray-300 px-4 py-2 text-left bg-gray-50">Durée</th>
                                <td class="border border-gray-300 px-4 py-2">{{ \Carbon\Carbon::parse($locPret->date_debut)->diffInDays(\Carbon\Carbon::parse($locPret->date_retour)) }} jours</td>
                            </tr>
                            <tr class="border border-gray-300">
                                <th class="border border-gray-300 px-4 py-2 text-left bg-gray-50">Statut</th>
                                <td class="border border-gray-300 px-4 py-2">
                                    @php
                                        $today = \Carbon\Carbon::now();
                                        $returnDate = \Carbon\Carbon::parse($locPret->date_retour);
                                        if ($today > $returnDate) {
                                            echo '<span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-red-600 rounded">En retard</span>';
                                        } else if ($today->diffInDays($returnDate) <= 3) {
                                            echo '<span class="inline-block px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-300 rounded">Retour imminent</span>';
                                        } else {
                                            echo '<span class="inline-block px-2 py-1 text-xs font-semibold text-green-700 bg-green-200 rounded">En cours</span>';
                                        }
                                    @endphp
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Informations client</h4>
                    <table class="w-full table-auto border-collapse border border-gray-300">
                        <tbody>
                            <tr class="border border-gray-300">
                                <th class="border border-gray-300 px-4 py-2 text-left bg-gray-50">Nom complet</th>
                                <td class="border border-gray-300 px-4 py-2">{{ $locPret->clients->nom }}</td>
                            </tr>
                            <tr class="border border-gray-300">
                                <th class="border border-gray-300 px-4 py-2 text-left bg-gray-50">Téléphone</th>
                                <td class="border border-gray-300 px-4 py-2">{{ $locPret->clients->numero_telephone ?? 'Non renseigné' }}</td>
                            </tr>
                            <tr class="border border-gray-300">
                                <th class="border border-gray-300 px-4 py-2 text-left bg-gray-50">Numéro client</th>
                                <td class="border border-gray-300 px-4 py-2">{{ $locPret->clients->code_client ?? 'Non renseignée' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <h4 class="text-lg font-semibold mb-4">PC associés</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numéro de série</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Caractéristiques</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($locPret->pcrenouvs as $pc)
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $pc->numero_serie }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $pc->reference }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $pc->type }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm">
                                @if ($pc->statut == 'loué')
                                    <span class="inline-block px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-200 rounded">Loué</span>
                                @elseif ($pc->statut == 'prêté')
                                    <span class="inline-block px-2 py-1 text-xs font-semibold text-indigo-800 bg-indigo-200 rounded">Prêté</span>
                                @else
                                    <span class="inline-block px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-300 rounded">{{ $pc->statut }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">{{ \Illuminate\Support\Str::limit($pc->caracteristiques, 50) }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm">
                                <a href="{{ route('gestrenouv.show', $pc->id) }}" class="inline-block px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600 transition">Voir</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">Aucun PC associé à cette location/prêt.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h5 class="text-lg font-semibold">Confirmation de suppression</h5>
            <button onclick="toggleModal('deleteModal')" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <p class="mb-6">Êtes-vous sûr de vouloir supprimer cette location/prêt ? Cette action libérera tous les PC associés et les remettra en stock.</p>
        <div class="flex justify-end space-x-3">
            <button onclick="toggleModal('deleteModal')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Annuler</button>
            <form action="{{ route('locpret.destroy', $locPret->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Supprimer</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal de retour de tous les PC -->
<div id="returnModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h5 class="text-lg font-semibold">Confirmation de retour</h5>
            <button onclick="toggleModal('returnModal')" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <p class="mb-6">Êtes-vous sûr de vouloir marquer tous les PC comme retournés ? Cette action supprimera également cet enregistrement de location/prêt.</p>
        <div class="flex justify-end space-x-3">
            <button onclick="toggleModal('returnModal')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Annuler</button>
            <form action="{{ route('locpret.retourner', $locPret->id) }}" method="POST" class="inline">
                @csrf
                @method('POST')
                <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">Confirmer le retour</button>
            </form>
        </div>
    </div>
</div>

<script>
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    if(modal.classList.contains('hidden')){
        modal.classList.remove('hidden');
    } else {
        modal.classList.add('hidden');
    }
}
</script>
@endsection
