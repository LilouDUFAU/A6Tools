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
        Schema::create('loc_prets', function (Blueprint $table) {
            $table->id();
            $table->date('date_debut'); // Date de début de la location / prêt
            $table->date('date_retour'); // Date de retour de la location / prêt
            $table->unsignedBigInteger('client_id'); // Clé étrangère vers la table clients

            // Clé étrangère vers la table clients
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loc_prets');
    }
};
