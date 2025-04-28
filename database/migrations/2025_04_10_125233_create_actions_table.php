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
        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->text('intitule'); // description de l'action
            $table->unsignedBigInteger('panne_id'); // ID de l'employé
            $table->unsignedBigInteger('user_id'); // ID de l'utilisateur
            $table->timestamps(); // created_at, updated_at

            // Clé étrangère vers la table des utilisateurs
           $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('panne_id')->references('id')->on('panne')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actions');
    }
};
