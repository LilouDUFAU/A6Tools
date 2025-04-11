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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // Nom du client
            $table->string('email')->unique(); // Email du client, unique
            $table->string('telephone'); // Téléphone du client
            $table->string('adresse_postale'); // Adresse postale du client
            $table->enum('type', ['particulier', 'entreprise']); // Type de client (particulier ou entreprise)
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
