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
        Schema::create('pcrenouv_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pcrenouv_id')->constrained('p_c_renouvs')->onDelete('cascade');
            $table->foreignId('stock_id')->constrained('stocks')->onDelete('cascade');
            $table->integer('quantite'); // Quantité de PCRenouv dans le stock
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        dropIfExists('pcrenouv_stock');
    }
};
