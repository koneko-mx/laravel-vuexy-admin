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
        Schema::create('vuexy_modules', function (Blueprint $table) {
            $table->string('slug')->primary(); // Ej. koneko-vuexy-contacts
            $table->string('name');            // Nombre completo del módulo
            $table->string('vendor')->nullable(); // koneko-st, vendor de composer

            $table->unsignedSmallInteger('installed_module_id')->nullable()->index();
            $table->string('version')->nullable(); // 1.0.0
            $table->string('build_version')->nullable(); // 20250429223531
            $table->string('type')->default('plugin'); // core, plugin, theme, etc.
            $table->string('provider')->nullable();
            $table->json('tags')->nullable();
            $table->json('metadata')->nullable(); // json libre para UI, etc.

            // Auditoria
            $table->boolean('is_enabled')->default(true)->index(); // Para activar/desactivar
            $table->boolean('is_installed')->default(false)->index(); // Para trackear instalación

            $table->unsignedMediumInteger('created_by')->nullable()->index();
            $table->unsignedMediumInteger('updated_by')->nullable()->index();
            $table->unsignedMediumInteger('deleted_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            // Indices
            $table->index(['is_enabled', 'is_installed']);

            // Relaciones
            $table->foreign('installed_module_id')->references('id')->on('installed_modules')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('deleted_by')->references('id')->on('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Elimina tablas solo si existen
        Schema::dropIfExists('vuexy_modules');
    }
};
