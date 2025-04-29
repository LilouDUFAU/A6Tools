@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto my-4 py-8 px-6 bg-white shadow-md rounded-lg">
    <h1 class="text-3xl font-extrabold text-gray-800 mb-8">Modifier la Panne</h1>

    <form action="{{ route('panne.update', $panne->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Partie Commande -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Panne</h2>

            <div class="mb-4">
                <label for="date_commande" class="block text-sm font-semibold text-gray-700">Date de commande</label>
                <input type="date" id="date_commande" name="date_commande" value="{{ old('date_commande', $panne->date_commande) }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
            </div>

            <div class="mb-4">
                <label for="date_panne" class="block text-sm font-semibold text-gray-700">Date de panne</label>
                <input type="date" id="date_panne" name="date_panne" value="{{ old('date_panne', $panne->date_panne) }}" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
            </div>

            <div class="mb-4">
                <label for="categorie_materiel" class="block text-sm font-semibold text-gray-700">Catégorie matériel</label>
                <textarea id="categorie_materiel" name="categorie_materiel" rows="4" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">{{ old('categorie_materiel', $panne->categorie_materiel) }}</textarea>
            </div>

            <div class="mb-4">
                <label for="categorie_panne" class="block text-sm font-semibold text-gray-700">Catégorie panne</label>
                <textarea id="categorie_panne" name="categorie_panne" rows="4" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">{{ old('categorie_panne', $panne->categorie_panne) }}</textarea>
            </div>

            <div class="mb-4">
                <label for="detail_panne" class="block text-sm font-semibold text-gray-700">Détail de la panne</label>
                <textarea id="detail_panne" name="detail_panne" rows="4" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">{{ old('detail_panne', $panne->detail_panne) }}</textarea>
            </div>
        </div>

        <!-- Partie Client -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Client</h2>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700">Client</label>
                <input
                    type="text"
                    id="client_search"
                    value="{{ old('client_id', $panne->clients->first()->nom ?? '') }}"
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1"
                    readonly
                    style="cursor: not-allowed;">
                
                <!-- Champ caché pour envoyer l'ID du client -->
                <input type="hidden" id="client_id" name="client_id" value="{{ old('client_id', $panne->clients->first()->id ?? '') }}">
            </div>

            <div id="selected_client" class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200 hidden">
                <h3 class="font-medium text-green-800">Client sélectionné</h3>
                <p id="selected_client_info" class="text-green-700"></p>
            </div>
        </div>

        <!-- Partie Fournisseur -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Fournisseur</h2>
            <div class="mb-4">
                <label for="fournisseur_id" class="block text-sm font-semibold text-gray-700">Fournisseur</label>
                <input
                    type="text"
                    value="{{ old('fournisseur_id', $panne->fournisseur->nom ?? '') }}"
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1"
                    readonly
                    style="cursor: not-allowed;">
            </div>
        </div>

        <!-- Partie Actions -->
        <div class="mb-4">
    <label for="actions" class="block text-sm font-semibold text-gray-700">Actions</label>
    <div id="actions-container">
        @foreach($panne->actions as $action)
            <div class="flex space-x-2 mb-2">
                <input
                    type="text"
                    name="actions[{{ $action->id }}]"
                    value="{{ $action->intitule }}"
                    placeholder="Action"
                    class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
            </div>
        @endforeach
        <div class="flex space-x-2 mb-2">
            <input type="text" name="new_actions[]" placeholder="Nouvelle action" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
            <button type="button" onclick="addActionField()" class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 focus:ring-2 focus:ring-green-500">+</button>
        </div>
    </div>
</div>

<script>
function addActionField() {
    const container = document.getElementById('actions-container');
    const newActionField = document.createElement('div');
    newActionField.className = 'flex space-x-2 mb-2';
    newActionField.innerHTML = `
        <input type="text" name="new_actions[]" placeholder="Nouvelle action" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
    `;
    container.appendChild(newActionField);
}
</script>
</script>
        </div>

        <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg shadow hover:bg-green-700 focus:ring-2 focus:ring-green-500 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Mettre à jour
        </button>
    </form>
    <div class="text-right mt-4 p-4">
        <a href="{{ route('panne.index') }}" class="text-gray-500 hover:underline">Retour</a>
    </div>
</div>

@endsection
