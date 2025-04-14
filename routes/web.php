<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/commandes', [CommandeController::class, 'index'])->name('commande.index');
    Route::get('/commandes/create', [CommandeController::class, 'create'])->name('commande.create');
    Route::post('/commandes', [CommandeController::class, 'store'])->name('commande.store');
    Route::get('/commandes/{id}', [CommandeController::class, 'show'])->name('commande.show');
    Route::get('/commandes/{id}/edit', [CommandeController::class, 'edit'])->name('commande.edit');
    Route::put('/commandes/{id}', [CommandeController::class, 'update'])->name('commande.update');
    Route::delete('/commandes/{id}', [CommandeController::class, 'destroy'])->name('commande.destroy');
});