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
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->string('intitule'); // Intitulé de la commande
            $table->decimal('prix_total', 10, 2); // Prix total de la commande
            $table->enum('etat', ['en_attente','en_cours', 'terminée', 'annulée']); // Etat de la commande
            $table->enum('urgence', ['pas urgent', 'peu urgent', 'moyennement urgent', 'urgent', 'très urgent']); // Urgence
            $table->text('remarque')->nullable(); // Remarques sur la commande
            $table->date('date_livraison_fournisseur'); // Date de livraison fournisseur
            $table->date('date_installation_prevue'); // Date d'installation prévue
            $table->unsignedBigInteger('client_id')->nullable(); // Clé étrangère vers la table clients
            $table->unsignedBigInteger('employe_id'); // Clé étrangère vers la table users
            $table->timestamps(); // created_at, updated_at

            // Clés étrangères
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('employe_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
