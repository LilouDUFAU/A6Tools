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
        Schema::create('loc_pret_p_c_renouv', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loc_pret_id');
            $table->unsignedBigInteger('p_c_renouv_id');

            $table->foreign('loc_pret_id')->references('id')->on('loc_prets')->onDelete('cascade');
            $table->foreign('p_c_renouv_id')->references('id')->on('p_c_renouvs')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
