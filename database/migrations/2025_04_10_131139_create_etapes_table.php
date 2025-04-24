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
        Schema::create('etapes', function (Blueprint $table) {
            $table->id();
            $table->string('intitule'); // Intitulé de l'étape
            $table->unsignedBigInteger('preparation_id'); // Clé étrangère vers la table prep_atelier
            $table->boolean('is_done')->default(false);
            $table->timestamps(); // created_at, updated_at

            // Clé étrangère vers la table prep_atelier
            $table->foreign('preparation_id')->references('id')->on('prep_ateliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etapes');
    }
};
