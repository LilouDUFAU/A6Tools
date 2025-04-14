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
            $table->string('reference'); // Référence du PCRenouv
            $table->integer('quantite'); // Quantité
            $table->text('caracteristiques'); // Caractéristiques du PCRenouv
            $table->enum('emplacement', ["Mont de Marsan", "Aire sur Adour"]); // Emplacement
            $table->enum('type', ['portable', 'fixe']); // Type du PCRenouv
            $table->enum('statut', ['en stock', 'prêté']); // Statut du PCRenouv
            $table->unsignedBigInteger('employe_id'); // Clé étrangère vers la table users (employé)
            $table->timestamps(); // created_at, updated_at

            // Clé étrangère vers la table users (employe_id)
            $table->foreign('employe_id')->references('id')->on('users')->onDelete('cascade');
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
