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
        Schema::create('prep_ateliers', function (Blueprint $table) {
            $table->id();
            $table->text('notes'); // Notes concernant la préparation
            $table->unsignedBigInteger('commande_id'); // Clé étrangère vers la table commandes
            $table->unsignedBigInteger('employe_id'); // Clé étrangère vers la table users (employé qui prépare l'atelier)
            $table->timestamps(); // created_at, updated_at

            // Clé étrangère vers la table commandes
            $table->foreign('commande_id')->references('id')->on('commandes')->onDelete('cascade');
            // Clé étrangère vers la table users (employé)
            $table->foreign('employe_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prep_ateliers');
    }
};
