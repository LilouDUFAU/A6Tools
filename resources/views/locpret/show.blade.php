@extends('layouts.app')

@section('content')

    <div class="max-w-6xl mx-auto my-4 py-8 px-6 bg-white shadow-md rounded-lg">
        <h1 class="text-3xl font-extrabold text-gray-800 mb-8 text-center">Détails de la Location/Prêt</h1>

        <!-- Partie Informations générales -->
        <div class="border-l-4 border-green-600 pl-4 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Informations générales</h2>
            <p><strong>Client :</strong> {{ $locPret->clients->nom }} {{ $locPret->clients->prenom }}</p>
            <p><strong>Date de début :</strong> {{ \Carbon\Carbon::parse($locPret->date_debut)->format('d/m/Y') }}</p>
            <p><strong>Date de retour prévue :</strong> {{ \Carbon\Carbon::parse($locPret->date_retour)->format('d/m/Y') }}</p>
            <p><strong>Durée :</strong> {{ \Carbon\Carbon::parse($locPret->date_debut)->diffInDays(\Carbon\Carbon::parse($locPret->date_retour)) }} jours</p>
            <p><strong>Statut :</strong>
                @php
                    $today = \Carbon\Carbon::now();
                    $startDate = \Carbon\Carbon::parse($locPret->date_debut);
                    $returnDate = \Carbon\Carbon::parse($locPret->date_retour);

                    if ($today < $startDate) {
                        echo '<span class="inline-block px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-200 rounded">À venir</span>';
                    } elseif ($today > $returnDate) {
                        echo '<span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-red-600 rounded">En retard</span>';
                    } elseif ($today->diffInDays($returnDate) <= 3) {
                        echo '<span class="inline-block px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-300 rounded">Retour imminent</span>';
                    } else {
                        echo '<span class="inline-block px-2 py-1 text-xs font-semibold text-green-700 bg-green-200 rounded">En cours</span>';
                    }
                @endphp
            </p>
        </div>

        <!-- Partie Informations client -->
        <div class="border-l-4 border-green-600 pl-4 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Informations client</h2>
            <p><strong>Nom complet :</strong> {{ $locPret->clients->nom }}</p>
            <p><strong>Numéro client :</strong> {{ $locPret->clients->code_client ?? 'Non renseigné' }}</p>
            <p><strong>Téléphone :</strong> {{ $locPret->clients->numero_telephone ?? 'Non renseigné' }}</p>
        </div>

        <!-- Partie PC associés -->
        <div class="border-l-4 border-green-600 pl-4 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">PC associés</h2>
            
            @forelse ($locPret->pcrenouvs as $pc)
                <div class="bg-gray-50 p-4 rounded-lg shadow mb-6">
                    <h3 class="text-xl font-bold underline mb-2 uppercase">{{ $pc->reference }}</h3>
                    <p><strong>Numéro de série :</strong> {{ $pc->numero_serie }}</p>
                    <p><strong>Type :</strong> {{ $pc->type }}</p>
                    <p><strong>Statut :</strong>
                        @if ($pc->statut == 'loué')
                            <span class="inline-block px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-200 rounded">Loué</span>
                        @elseif ($pc->statut == 'prêté')
                            <span class="inline-block px-2 py-1 text-xs font-semibold text-indigo-800 bg-indigo-200 rounded">Prêté</span>
                        @else
                            <span class="inline-block px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-300 rounded">{{ $pc->statut }}</span>
                        @endif
                    </p>
                    <p><strong>Caractéristiques :</strong> {{ $pc->caracteristiques }}</p>
                    <div class="mt-2">
                        <a href="{{ route('gestrenouv.show', $pc->id) }}" class="text-blue-600 font-medium hover:underline">Voir le PC</a>
                    </div>
                </div>
            @empty
                <p>Aucun PC associé à cette location/prêt.</p>
            @endforelse
        </div>

        <!-- Boutons -->
        <div class="flex flex-col sm:flex-row justify-between mt-8 space-y-4 sm:space-y-0">
            <a href="{{ route('locpret.edit', $locPret->id) }}" class="text-green-600 font-medium hover:underline">Modifier</a>
            <a href="{{ route('locpret.index') }}" class="text-gray-600 hover:underline">Retour à la liste</a>
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
                    @method('PUT')
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