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
        Schema::create('bon_livraisons', function (Blueprint $table) {
            $table->bonLivraisonId();
            $table->enum('statut', ['en_attente', 'signé', 'annulé']); // Champ statut en enum
            $table->timestamp('date_signature')->nullable(); // Date de signature (peut être null si pas encore signé)
            $table->unsignedBigInteger('commande_id'); // ID de la commande associée
            $table->timestamps(); // created_at, updated_at

            // Clé étrangère vers la table des commandes
            $table->foreign('commande_id')->references('id')->on('commandes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bon_livraisons');
    }
};
