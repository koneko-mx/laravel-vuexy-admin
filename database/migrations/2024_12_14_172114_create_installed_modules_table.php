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
        Schema::create('installed_modules', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->unsignedSmallInteger('module_package_id')->index(); // Relación con module_packages
            $table->string('slug')->unique();                            // Ej: vuexy-website-admin
            $table->string('name');                                      // Ej: koneko/laravel-vuexy-website-admin
            $table->string('version')->nullable();                       // Versión instalada
            $table->string('install_path')->nullable();                  // Path en vendor/ o custom
            $table->boolean('enabled')->default(true);                   // Activado para el sistema

            // Control
            $table->json('install_options')->nullable();                // flags de instalación zip, git, etc.

            // Auditoría y estado
            $table->timestamp('installed_at')->nullable();
            $table->timestamp('last_checked_at')->nullable();            // Sincronización con fuente

            // Auditoría
            $table->unsignedMediumInteger('created_by')->nullable()->index();
            $table->unsignedMediumInteger('updated_by')->nullable()->index();
            $table->unsignedMediumInteger('deleted_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            // Indices
            $table->index(['module_package_id', 'enabled']);

            // Relaciones
            $table->foreign('module_package_id')->references('id')->on('module_packages')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('deleted_by')->references('id')->on('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installed_modules');
    }
};
