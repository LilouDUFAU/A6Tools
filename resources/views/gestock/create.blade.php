use App\Http\Controllers\CommandeController;

// Afficher la liste des commandes
Route::get('/commandes', [CommandeController::class, 'index'])->name('commande.index');

// Afficher le formulaire de création
Route::get('/commandes/create', [CommandeController::class, 'create'])->name('commande.create');

// Enregistrer une nouvelle commande
Route::post('/commandes', [CommandeController::class, 'store'])->name('commande.store');

// Afficher les détails d’une commande spécifique
Route::get('/commandes/{id}', [CommandeController::class, 'show'])->name('commande.show');

// Afficher le formulaire d'édition d'une commande
Route::get('/commandes/{id}/edit', [CommandeController::class, 'edit'])->name('commande.edit');

// Mettre à jour une commande
Route::put('/commandes/{id}', [CommandeController::class, 'update'])->name('commande.update');
Route::patch('/commandes/{id}', [CommandeController::class, 'update']); // optionnel si tu veux PATCH aussi

// Supprimer une commande
Route::delete('/commandes/{id}', [CommandeController::class, 'destroy'])->name('commande.destroy');
