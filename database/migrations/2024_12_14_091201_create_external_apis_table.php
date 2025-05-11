<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('external_apis', function (Blueprint $table) {
            $table->id();

            // Identidad y relación
            $table->string('slug')->unique();               // api-google-analytics
            $table->string('name');                         // Google Analytics
            $table->string('module')->index();              // vuexy-website-admin
            $table->string('provider')->nullable()->index(); // Google, Twitter, Banxico

            // Autenticación y alcance
            $table->string('auth_type', 16)->nullable()->index();        // api_key, oauth2, jwt, none
            $table->json('credentials')->nullable();        // api_key, secret, token, etc.
            $table->json('scopes')->nullable();             // ['read', 'write', 'analytics']

            // Conectividad
            $table->string('base_url')->nullable();         // https://api.example.com
            $table->string('doc_url')->nullable();          // https://docs.example.com

            // Estado y entorno
            $table->string('environment')->default('production')->index(); // dev, staging, prod
            $table->boolean('is_active')->default(true)->index();          // toggle global

            // Extensión dinámica
            $table->json('metadata')->nullable();           // libre: headers extra, tags, categorías
            $table->json('config')->nullable();             // libre: UI params, rate limits, etc.

            // Auditoría
            $table->timestamps();

            $table->unsignedMediumInteger('created_by')->nullable()->index();
            $table->unsignedMediumInteger('updated_by')->nullable()->index();

            // Relaciones
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_apis');
    }
};
