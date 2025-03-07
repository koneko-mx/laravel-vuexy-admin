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
        Schema::create('media_items', function (Blueprint $table) {
            $table->mediumIncrements('id');

            // Relación polimórfica
            $table->unsignedMediumInteger('mediaable_id');
            $table->string('mediaable_type');

            $table->unsignedTinyInteger('type')->index(); // Tipo de medio: 'image', 'video', 'file', 'youtube'
            $table->unsignedTinyInteger('sub_type')->index(); // Subtipo de medio: 'thumbnail', 'main', 'additional'

            $table->string('url', 255)->nullable(); // URL del medio
            $table->string('path')->nullable(); // Ruta del archivo si está almacenado localmente

            $table->string('title')->nullable()->index(); // Título del medio
            $table->mediumText('description')->nullable(); // Descripción del medio
            $table->unsignedTinyInteger('order')->nullable(); // Orden de presentación

            // Authoría
            $table->timestamps();

            // Índices
            $table->index(['mediaable_type', 'mediaable_id']);
            $table->index(['mediaable_type', 'mediaable_id', 'type']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
