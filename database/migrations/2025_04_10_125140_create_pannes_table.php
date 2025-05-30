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
        Schema::create('pannes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_sav')->unique()->nullable(); // Numéro de SAV
            $table->enum('statut', ['En attente', 'Remboursement', 'Transit', 'Envoyé', 'Échange anticipé']); // Statut de la panne
            $table->string('demande'); // Demande du client
            $table->enum('etat_client', ['Ordi de prêt', 'Échangé', 'En attente']); // Etat de la panne vu par le client
            $table->string('categorie_materiel')->nullable(); // Catégorie du matériel concerné par la panne
            $table->string('categorie_panne')->nullable(); // Catégorie de la panne
            $table->text('detail_panne')->nullable(); // Détails sur la panne
            $table->date('date_commande')->nullable(); // Date de la commande du matériel
            $table->date('date_panne')->nullable(); // Date de la panne
            $table->unsignedBigInteger('fournisseur_id'); // Clé étrangère vers la table fournisseurs
            $table->timestamps(); // created_at, updated_at

            // Clé étrangère vers la table fournisseurs
            $table->foreign('fournisseur_id')->references('id')->on('fournisseurs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pannes');
    }
};
