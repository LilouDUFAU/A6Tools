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
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // Nom du produit
            $table->text('description'); // Description du produit
            $table->text('caracteristiques_techniques'); // Caractéristiques techniques du produit
            $table->string('reference')->unique(); // Référence unique pour le produit
            $table->decimal('prix', 8, 2); // Prix du produit
            $table->string('image')->nullable(); // URL de l'image du produit (optionnel)
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
