@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto my-4 py-8 px-6 bg-white shadow-md rounded-lg">
    <h1 class="text-3xl font-extrabold text-gray-800 mb-8">Modifier le PCRenouv</h1>

    <form action="{{ route('gestrenouv.update', $pcrenouv->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Commande</h2>

            <div class="mb-4">
                <label for="reference" class="block text-gray-700 font-bold mb-2">Référence</label>
                <input type="text" name="reference" id="reference"
                    value="{{ old('reference', $pcrenouv->reference) }}"
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1"
                    required maxlength="255">
            </div>

            <div class="mb-4">
                <label for="quantite" class="block text-gray-700 font-bold mb-2">Quantité</label>
                <input type="number" name="quantite" id="quantite"
                    value="{{ old('quantite', $pcrenouv->stocks->first()?->pivot->quantite) }}"
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1"
                    required>
            </div>

            <div class="mb-4">
                <label for="caracteristiques" class="block text-gray-700 font-bold mb-2">Caractéristique</label>
                <input type="text" name="caracteristiques" id="caracteristiques"
                    value="{{ old('caracteristiques', $pcrenouv->caracteristiques) }}"
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1"
                    maxlength="255">
            </div>

            <div class="mb-4">
                <label for="type" class="block text-gray-700 font-bold mb-2">Type</label>
                <select id="type" name="type"
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1"
                    required>
                    <option value="">-- Sélectionner un type --</option>
                    @foreach ($type as $typeOption)
                        <option value="{{ $typeOption }}" {{ old('type', $pcrenouv->type) == $typeOption ? 'selected' : '' }}>
                            {{ ucfirst($typeOption) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="statut" class="block text-gray-700 font-bold mb-2">Statut</label>
                <select id="statut" name="statut"
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1"
                    required>
                    <option value="">-- Sélectionner un statut --</option>
                    @foreach ($statut as $statutOption)
                        <option value="{{ $statutOption }}" {{ old('statut', $pcrenouv->statut) == $statutOption ? 'selected' : '' }}>
                            {{ ucfirst($statutOption) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="border-l-4 border-green-600 pl-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Magasin</h2>
            <div class="mb-4">
                <label for="stock_id" class="block text-sm font-semibold text-gray-700">Choisir un site</label>
                <select id="stock_id" name="stock_id"
                    class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 px-2 py-1"
                    required>
                    <option value="">-- Sélectionner un site --</option>
                    @foreach (\App\Models\Stock::all() as $stock)
                        <option value="{{ $stock->id }}" {{ old('stock_id', $pcrenouv->stocks->first()?->id) == $stock->id ? 'selected' : '' }}>
                            {{ ucfirst($stock->lieux) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit"
                class="w-full bg-green-600 text-white px-6 py-3 rounded-lg shadow hover:bg-green-700 focus:ring-2 focus:ring-green-500 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5 13l4 4L19 7" />
                </svg>
                Mettre à jour
            </button>
        </div>
    </form>

    <div class="text-right mt-4 p-4">
        <a href="{{ route('gestrenouv.index') }}" class="text-gray-500 hover:underline">Retour</a>
    </div>
</div>
@endsection
