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
            $table->actionId();
            $table->enum('type', ['ajout', 'modification', 'suppression']); // Champ type en enum
            $table->text('description'); // description de l'action
            $table->unsignedBigInteger('employe_id'); // ID de l'employé
            $table->timestamps(); // created_at, updated_at

            // Clé étrangère vers la table des utilisateurs
            $table->foreign('employe_id')->references('id')->on('users')->onDelete('cascade');
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
