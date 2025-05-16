@extends('layouts.app')
@section('content')
<div class="max-w-6xl mx-auto my-4 py-8 px-6 bg-white shadow-md rounded-lg">
<h1 class="text-3xl font-extrabold text-gray-800 mb-8">Modifier PC Renouv</h1>
    <form action="{{ route('gestrenouv.update', $pcRenouv) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Partie Informations de Base -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Informations de Base</h2>

            <div class="mb-4">
                <label for="numero_serie" class="block text-sm font-semibold text-gray-700">* Numéro de série</label>
                <input type="text" id="numero_serie" name="numero_serie" value="{{ old('numero_serie', $pcRenouv->numero_serie) }}" 
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                @error('numero_serie')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="reference" class="block text-sm font-semibold text-gray-700">* Référence</label>
                <input type="text" id="reference" name="reference" value="{{ old('reference', $pcRenouv->reference) }}" 
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                @error('reference')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="quantite" class="block text-sm font-semibold text-gray-700">* Quantité</label>
                <input type="number" id="quantite" name="quantite" value="{{ old('quantite', $pcRenouv->quantite) }}" min="1" 
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                @error('quantite')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="type" class="block text-sm font-semibold text-gray-700">* Type</label>
                <select id="type" name="type" required
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                    <option value="">-- Sélectionner un type --</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ old('type', $pcRenouv->type) == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="statut" class="block text-sm font-semibold text-gray-700">* Statut</label>
                <select id="statut" name="statut" required
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                    <option value="">-- Sélectionner un statut --</option>
                    @foreach($statuts as $statut)
                        <option value="{{ $statut }}" {{ old('statut', $pcRenouv->statut) == $statut ? 'selected' : '' }}>{{ ucfirst($statut) }}</option>
                    @endforeach
                </select>
                @error('statut')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Partie Stock -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Magasin</h2>
            
            <div class="mb-4">
                <label for="stock_id" class="block text-sm font-semibold text-gray-700">* Choisir un site</label>
                <select id="stock_id" name="stock_id" required
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">
                    <option value="">-- Sélectionner un site --</option>
                    @foreach($stocks as $stock)
                        <option value="{{ $stock->id }}" 
                            {{ old('stock_id', $pcRenouv->stocks->first()?->id) == $stock->id ? 'selected' : '' }}>
                            {{ $stock->lieux }}
                        </option>
                    @endforeach
                </select>
                @error('stock_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="stock_quantite" class="block text-sm font-semibold text-gray-700">* Quantité en stock</label>
                <input type="number" id="stock_quantite" name="stock_quantite" min="1"
                    value="{{ old('stock_quantite', $pcRenouv->stocks->first()->pivot->quantite ?? 1) }}"
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1" required>
                @error('stock_quantite')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Partie Caractéristiques -->
        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Caractéristiques Techniques</h2>
            
            <div class="mb-4">
                <label for="caracteristiques" class="block text-sm font-semibold text-gray-700">* Caractéristiques</label>
                <textarea id="caracteristiques" name="caracteristiques" rows="4" required
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1">{{ old('caracteristiques', $pcRenouv->caracteristiques) }}</textarea>
                @error('caracteristiques')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg shadow hover:bg-green-700 focus:ring-2 focus:ring-green-500 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <polyline points="17 21 17 13 7 13 7 21"/>
                <polyline points="7 3 7 8 15 8"/>
            </svg>
            Mettre à jour
        </button>
    </form>
    <div class="text-right mt-4 p-4">
        <a href="{{ route('gestrenouv.index') }}" class="text-gray-500 hover:underline">Retour</a>
    </div>
</div>
@endsection