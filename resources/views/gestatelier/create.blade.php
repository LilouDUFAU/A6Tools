@extends('layouts.app')
@section('content')
<div class="max-w-6xl mx-auto my-4 py-8 px-6 bg-white shadow-md rounded-lg">
    <h1 class="text-3xl font-extrabold text-gray-800 mb-8">Nouvelle Préparation</h1>
    <form action="{{ route('prepatelier.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Préparation</h2>
            <div class="mb-4">
                <label for="notes" class="block text-gray-700 font-bold mb-2">Notes</label>
                <textarea name="notes" id="notes" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1" required maxlength="255" rows="4"></textarea>
            </div>
        </div>

        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Commandes</h2>
            <div class="mb-4">
                <label for="commande_id" class="block text-sm font-semibold text-gray-700">Choisir une commande</label>
                <select id="commande_id" name="commande_id" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm px-2 py-1">
                    <option value="">-- Sélectionner une commande --</option>
                    @foreach ($commandes as $commande)
                        <option value="{{ $commande->id }}" {{ old('commande_id') == $commande->id ? 'selected' : '' }}>
                            Commande n°{{ $commande->id }} : {{ $commande->client->nom }} ({{ $commande->client->code_client }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="border-l-4 border-green-600 pl-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">To do list</h2>
        <div id="etapes-container">
            <div class="etape-item mb-4 flex items-center">
            <input type="checkbox" name="etapes_done[]" value="0" class="mr-2">
            <input type="text" name="etapes[]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1" placeholder="Description de l'étape" required>
            </div>
        </div>
        <div class="text-right">
            <button type="button" id="add-etape-btn" class="mt-2 bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700  ">
            Ajouter une étape
            </button>
        </div>

        <script>
            document.getElementById('add-etape-btn').addEventListener('click', function () {
            const container = document.getElementById('etapes-container');
            const newEtape = document.createElement('div');
            newEtape.classList.add('etape-item', 'mb-4', 'flex', 'items-center');
            newEtape.innerHTML = `
            <input type="checkbox" name="etapes_done[]" value="0" class="mr-2">
            <input type="text" name="etapes[]" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm   px-2 py-1" placeholder="Description de l'étape" required>
            `;
            container.appendChild(newEtape);
            });
        </script>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg shadow hover:bg-green-700   flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Enregistrer
            </button>
        </div>
    </form>
    
    <div class="text-right mt-4 p-4">
        <a href="{{ route('prepatelier.index') }}" class="text-gray-500 hover:underline">Retour</a>
    </div>
</div>
@endsection
