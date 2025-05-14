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
            $table->date('date_pret'); // Date de prêt
            $table->date('date_retour')->nullable(); // Date de retour
            $table->unsignedBigInteger('client_id'); // Clé étrangère vers la table clients
            $table->timestamps();

            // Clé étrangère vers la table clients
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
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
