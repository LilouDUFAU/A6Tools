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
            $table->enum('etat_client', ['ordi de pret', 'échangé', 'en attente']); // Etat de la panne vu par le client
            $table->string('categorie_materiel'); // Catégorie du matériel concerné par la panne
            $table->string('categorie_panne'); // Catégorie de la panne
            $table->text('detail_panne'); // Détails sur la panne
            $table->date('date_panne'); // Date de la panne
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
