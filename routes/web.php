<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PCRenouvController;
use App\Http\Controllers\PrepAtelierController;
use App\Http\Controllers\EtapeController;
use App\Http\Controllers\PanneController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/gestock', [CommandeController::class, 'index'])->name('gestock.index');
    Route::get('/gestock/create', [CommandeController::class, 'create'])->name('gestock.create');
    Route::post('/gestock', [CommandeController::class, 'store'])->name('gestock.store');
    Route::get('/gestock/{id}', [CommandeController::class, 'show'])->name('gestock.show');
    Route::get('/gestock/{id}/edit', [CommandeController::class, 'edit'])->name('gestock.edit');
    Route::put('/gestock/{id}', [CommandeController::class, 'update'])->name('gestock.update');
    Route::delete('/gestock/{id}', [CommandeController::class, 'destroy'])->name('gestock.destroy');

    Route::get('/gestrenouv', [PCRenouvController::class, 'index'])->name('gestrenouv.index');
    Route::get('/gestrenouv/create', [PCRenouvController::class, 'create'])->name('gestrenouv.create');
    Route::post('/gestrenouv', [PCRenouvController::class, 'store'])->name('gestrenouv.store');
    Route::get('/gestrenouv/{id}', [PCRenouvController::class, 'show'])->name('gestrenouv.show');
    Route::get('/gestrenouv/{id}/edit', [PCRenouvController::class, 'edit'])->name('gestrenouv.edit');
    Route::put('/gestrenouv/{id}', [PCRenouvController::class, 'update'])->name('gestrenouv.update');
    Route::delete('/gestrenouv/{id}', [PCRenouvController::class, 'destroy'])->name('gestrenouv.destroy');
    Route::get('/gestrenouv{id}/louer', [PCRenouvController::class, 'louer'])->name('gestrenouv.louer');
    Route::get('/gestrenouv{id}/preter', [PCRenouvController::class, 'preter'])->name('gestrenouv.preter');
    Route::put('/gestrenouv/{id}/addLocPret', [PCRenouvController::class, 'addLocPret'])->name('gestrenouv.addLocPret');
    Route::put('/gestrenouv/{id}/retour', [PCRenouvController::class, 'retour'])->name('gestrenouv.retour');

    Route::get('/gestatelier', [PrepAtelierController::class, 'index'])->name('gestatelier.index');
    Route::get('/gestatelier/create', [PrepAtelierController::class, 'create'])->name('gestatelier.create');
    Route::post('/gestatelier', [PrepAtelierController::class, 'store'])->name('gestatelier.store');
    Route::get('/gestatelier/{id}', [PrepAtelierController::class, 'show'])->name('gestatelier.show');
    Route::get('/gestatelier/{id}/edit', [PrepAtelierController::class, 'edit'])->name('gestatelier.edit');
    Route::put('/gestatelier/{id}', [PrepAtelierController::class, 'update'])->name('gestatelier.update');
    Route::delete('/gestatelier/{id}', [PrepAtelierController::class, 'destroy'])->name('gestatelier.destroy');

    Route::get('/gestsav', [PanneController::class, 'index'])->name('gestsav.index');
    Route::get('/gestsav/create', [PanneController::class, 'create'])->name('gestsav.create');
    Route::post('/gestsav', [PanneController::class, 'store'])->name('gestsav.store');
    Route::get('/gestsav/{id}', [PanneController::class, 'show'])->name('gestsav.show');
    Route::get('/gestsav/{id}/edit', [PanneController::class, 'edit'])->name('gestsav.edit');
    Route::put('/gestsav/{id}', [PanneController::class, 'update'])->name('gestsav.update');
    Route::delete('/gestsav/{id}', [PanneController::class, 'destroy'])->name('gestsav.destroy');

    Route::put('/etapes/{id}/update', [EtapeController::class, 'update'])->name('etapes.update');


    Route::get('/gestuser', [UserController::class, 'index'])->name('gestuser.index');
    Route::get('/gestuser/create', [UserController::class, 'create'])->name('gestuser.create');    
    Route::post('/gestuser', [UserController::class, 'store'])->name('gestuser.store');
    Route::get('/gestuser/{id}', [UserController::class, 'show'])->name('gestuser.show');
    Route::get('/gestuser/{id}/edit', [UserController::class, 'edit'])->name('gestuser.edit');
    Route::put('/gestuser/{id}', [UserController::class, 'update'])->name('gestuser.update');
    Route::delete('/gestuser/{id}', [UserController::class, 'destroy'])->name('gestuser.destroy');
});