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
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->fournisseurId();
            $table->string('nom'); // Nom du fournisseur
            $table->string('email')->nullable(); // Email du fournisseur
            $table->string('telephone')->nullable(); // Téléphone du fournisseur
            $table->string('adresse_postale')->nullable(); // Adresse postale du fournisseur
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fournisseurs');
    }
};
