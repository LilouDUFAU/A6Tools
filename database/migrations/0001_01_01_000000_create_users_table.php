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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('telephone');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('photo')->nullable();
            $table->unsignedBigInteger('service_id')->default(1); 
            $table->unsignedBigInteger('role_id')->default(1);
            $table->unsignedBigInteger('stock_id');
            $table->rememberToken();
            $table->timestamps();

            // Clé étrangère vers la table services
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            // Clé étrangère vers la table roles
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            // Clé étrangère vers la table stocks
            $table->foreign('stock_id')->references('id')->on('stocks')->onDelete('cascade');

        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
