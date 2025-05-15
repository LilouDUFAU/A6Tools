@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto my-6 py-8 px-6 bg-white shadow-md rounded-lg">
    <h1 class="text-3xl font-extrabold text-gray-800 mb-8">Modifier PC Renouv</h1>

    <form action="{{ route('gestrenouv.update', $pcRenouv) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Partie Identité -->
        <div class="border-l-4 border-blue-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Informations générales</h2>

            <div class="mb-4">
                <label for="numero_serie" class="block text-sm font-semibold text-gray-700">Numéro de série</label>
                <input type="text" id="numero_serie" name="numero_serie" value="{{ old('numero_serie', $pcRenouv->numero_serie) }}" 
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-2 py-1 @error('numero_serie') border-red-500 @enderror" required>
                @error('numero_serie')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="reference" class="block text-sm font-semibold text-gray-700">Référence</label>
                <input type="text" id="reference" name="reference" value="{{ old('reference', $pcRenouv->reference) }}" 
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-2 py-1 @error('reference') border-red-500 @enderror" required>
                @error('reference')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="quantite" class="block text-sm font-semibold text-gray-700">Quantité</label>
                <input type="number" id="quantite" name="quantite" value="{{ old('quantite', $pcRenouv->quantite) }}" min="1" 
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-2 py-1 @error('quantite') border-red-500 @enderror" required>
                @error('quantite')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Partie Stock -->
        <div class="border-l-4 border-blue-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Stock</h2>

            <div class="mb-4">
                <label for="stock_id" class="block text-sm font-semibold text-gray-700">Sélectionner un stock</label>
                <select id="stock_id" name="stock_id" required
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-2 py-1 @error('stock_id') border-red-500 @enderror">
                    <option value="">-- Choisir --</option>
                    @foreach($stocks as $stock)
                        <option value="{{ $stock->id }}" 
                            {{ old('stock_id', $pcRenouv->stocks->first()?->id) == $stock->id ? 'selected' : '' }}>
                            {{ $stock->lieux }}
                        </option>
                    @endforeach
                </select>
                @error('stock_id')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="stock_quantite" class="block text-sm font-semibold text-gray-700">Quantité en stock</label>
                <input type="number" id="stock_quantite" name="stock_quantite" min="1"
                    value="{{ old('stock_quantite', $pcRenouv->stocks->first()->pivot->quantite ?? 1) }}"
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-2 py-1 @error('stock_quantite') border-red-500 @enderror" required>
                @error('stock_quantite')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Partie Description -->
        <div class="border-l-4 border-blue-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Caractéristiques</h2>

            <div class="mb-4">
                <label for="caracteristiques" class="block text-sm font-semibold text-gray-700">Description détaillée</label>
                <textarea id="caracteristiques" name="caracteristiques" rows="4" required
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-2 py-1 @error('caracteristiques') border-red-500 @enderror">{{ old('caracteristiques', $pcRenouv->caracteristiques) }}</textarea>
                @error('caracteristiques')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Partie Type & Statut -->
        <div class="border-l-4 border-blue-600 pl-4 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="type" class="block text-sm font-semibold text-gray-700">Type</label>
                <select id="type" name="type" required
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-2 py-1 @error('type') border-red-500 @enderror">
                    <option value="">-- Sélectionner un type --</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ old('type', $pcRenouv->type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
                @error('type')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="statut" class="block text-sm font-semibold text-gray-700">Statut</label>
                <select id="statut" name="statut" required
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-2 py-1 @error('statut') border-red-500 @enderror">
                    <option value="">-- Sélectionner un statut --</option>
                    @foreach($statuts as $statut)
                        <option value="{{ $statut }}" {{ old('statut', $pcRenouv->statut) == $statut ? 'selected' : '' }}>{{ $statut }}</option>
                    @endforeach
                </select>
                @error('statut')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Bouton -->
        <div class="flex justify-end">
            <button type="submit"
                class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Mettre à jour
            </button>
        </div>

    </form>

    <div class="text-right mt-6">
        <a href="{{ route('gestrenouv.index') }}" class="text-gray-500 hover:underline">Retour à la liste</a>
    </div>
</div>

@endsection
