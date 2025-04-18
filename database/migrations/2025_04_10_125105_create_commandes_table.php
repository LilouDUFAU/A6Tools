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
            $table->enum('etat', ['A faire','Commandé', 'Reçu', 'Prévenu', 'Délais']); // Etat de la commande
            $table->enum('urgence', ['pas urgent', 'urgent', 'très urgent']); // Urgence
            $table->text('remarque')->nullable(); // Remarques sur la commande
            $table->integer('delai_installation')->nullable(); // Date de livraison fournisseur
            $table->date('date_installation_prevue')->nullable(); // Date d'installation prévue
            $table->string('reference_devis')->nullable(); // Référence du devis
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
