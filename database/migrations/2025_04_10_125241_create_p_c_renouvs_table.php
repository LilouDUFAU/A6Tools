<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('p_c_renouvs', function (Blueprint $table) {
            $table->id();
            $table->string('numero_serie')->unique(); // Numéro de série du PCRenouv
            $table->string('reference'); // Référence du PCRenouv
            $table->integer('quantite'); // Quantité
            $table->string('caracteristiques', 5000)->nullable(); // Caractéristiques du PCRenouv
            $table->enum('type', ['portable', 'fixe']); // Type du PCRenouv
            $table->enum('statut', ['en stock', 'prêté', 'loué']); // Statut du PCRenouv
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_c_renouvs');
    }
};
