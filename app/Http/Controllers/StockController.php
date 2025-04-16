<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Affiche tous les stocks.
     */
    public function index()
    {
        $stocks = Stock::all();
        return view('stocks.index', compact('stocks'));
    }

    /**
     * Affiche le formulaire de création d'un stock.
     */
    public function create()
    {
        $lieuxDisponibles = Stock::LIEUX;
        return view('stocks.create', compact('lieuxDisponibles'));
    }

    /**
     * Enregistre un nouveau stock.
     */
    public function store(Request $request)
    {
        $request->validate([
            'lieux' => 'required|in:' . implode(',', Stock::LIEUX),
        ]);

        Stock::create([
            'lieux' => $request->lieux,
        ]);

        return redirect()->route('stocks.index')->with('success', 'Stock ajouté avec succès.');
    }

    /**
     * Affiche les détails d’un stock.
     */
    public function show(string $id)
    {
        $stock = Stock::findOrFail($id);
        return view('stocks.show', compact('stock'));
    }

    /**
     * Affiche le formulaire d'édition d'un stock.
     */
    public function edit(string $id)
    {
        $stock = Stock::findOrFail($id);
        $lieuxDisponibles = Stock::LIEUX;
        return view('stocks.edit', compact('stock', 'lieuxDisponibles'));
    }

    /**
     * Met à jour un stock existant.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'lieux' => 'required|in:' . implode(',', Stock::LIEUX),
        ]);

        $stock = Stock::findOrFail($id);
        $stock->update([
            'lieux' => $request->lieux,
        ]);

        return redirect()->route('stocks.index')->with('success', 'Stock mis à jour avec succès.');
    }

    /**
     * Supprime un stock.
     */
    public function destroy(string $id)
    {
        $stock = Stock::findOrFail($id);
        $stock->delete();

        return redirect()->route('stocks.index')->with('success', 'Stock supprimé avec succès.');
    }
}
