<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PCRenouvController; // Assurez-vous d'importer le contrôleur

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

    // Routes pour PCRenouvController accessibles uniquement aux utilisateurs connectés
    Route::get('/pcrenouv', [PCRenouvController::class, 'index'])->name('gestrenouv.index');
    Route::get('/pcrenouv/create', [PCRenouvController::class, 'create'])->name('gestrenouv.create');
    Route::post('/pcrenouv', [PCRenouvController::class, 'store'])->name('gestrenouv.store');
    Route::get('/pcrenouv/{id}', [PCRenouvController::class, 'show'])->name('gestrenouv.show');
    Route::get('/pcrenouv/{id}/edit', [PCRenouvController::class, 'edit'])->name('gestrenouv.edit');
    Route::put('/pcrenouv/{id}', [PCRenouvController::class, 'update'])->name('gestrenouv.update');
    Route::delete('/pcrenouv/{id}', [PCRenouvController::class, 'destroy'])->name('gestrenouv.destroy');
    Route::get('/pcrenouv{id}/louer', [PCRenouvController::class, 'louer'])->name('gestrenouv.louer');
    Route::get('/pcrenouv{id}/preter', [PCRenouvController::class, 'preter'])->name('gestrenouv.preter');
    Route::put('/gestrenouv/{id}/addLocPret', [PCRenouvController::class, 'addLocPret'])->name('gestrenouv.addLocPret');
    Route::put('/pcrenouv/{id}/retour', [PCRenouvController::class, 'retour'])->name('gestrenouv.retour');

    
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AccountController::class, 'index'])->name('admin.index');
    Route::get('/admin/create', [AccountController::class, 'create'])->name('admin.create');
    Route::post('/admin', [AccountController::class, 'store'])->name('admin.store');
    Route::get('/admin/{id}/edit', [AccountController::class, 'edit'])->name('admin.edit');
    Route::put('/admin/{id}', [AccountController::class, 'update'])->name('admin.update');
    Route::delete('/admin/{id}', [AccountController::class, 'destroy'])->name('admin.destroy');
});