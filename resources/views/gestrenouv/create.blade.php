<!-- resources/views/gestrenouv/create.blade.php -->
@extends('layouts.app')

@section('title', 'Ajouter un PC Renouv')

@section('content')
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 bg-gray-50 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-800">Ajouter un PC Renouv</h1>
        </div>

        <div class="p-6">
            <form action="{{ route('gestrenouv.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="numero_serie" class="block text-sm font-medium text-gray-700">Numéro de série</label>
                        <input type="text" name="numero_serie" id="numero_serie" 
                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                               value="{{ old('numero_serie') }}" required>
                        @error('numero_serie')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="reference" class="block text-sm font-medium text-gray-700">Référence</label>
                        <input type="text" name="reference" id="reference" 
                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                               value="{{ old('reference') }}" required>
                        @error('reference')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="quantite" class="block text-sm font-medium text-gray-700">Quantité</label>
                        <input type="number" name="quantite" id="quantite" min="1"
                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                               value="{{ old('quantite', 1) }}" required>
                        @error('quantite')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" id="type"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                required>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="statut" class="block text-sm font-medium text-gray-700">Statut</label>
                        <select name="statut" id="statut"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                required>
                            @foreach($statuts as $statut)
                                <option value="{{ $statut }}" {{ old('statut', 'en stock') == $statut ? 'selected' : '' }}>{{ ucfirst($statut) }}</option>
                            @endforeach
                        </select>
                        @error('statut')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="stock_id" class="block text-sm font-medium text-gray-700">Stock</label>
                        <select name="stock_id" id="stock_id"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                required>
                            <option value="">Sélectionner un stock</option>
                            @foreach($stocks as $stock)
                                <option value="{{ $stock->id }}" {{ old('stock_id') == $stock->id ? 'selected' : '' }}>{{ $stock->lieux }}</option>
                            @endforeach
                        </select>
                        @error('stock_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="stock_quantite" class="block text-sm font-medium text-gray-700">Quantité en stock</label>
                        <input type="number" name="stock_quantite" id="stock_quantite" min="1"
                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                               value="{{ old('stock_quantite', 1) }}" required>
                        @error('stock_quantite')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="caracteristiques" class="block text-sm font-medium text-gray-700">Caractéristiques</label>
                    <textarea name="caracteristiques" id="caracteristiques" rows="4"
                              class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                              required>{{ old('caracteristiques') }}</textarea>
                    @error('caracteristiques')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-8 flex items-center justify-end space-x-3">
                    <a href="{{ route('gestrenouv.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection