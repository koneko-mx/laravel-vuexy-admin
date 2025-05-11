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
        Schema::create('module_packages', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->string('name')->unique();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->jsonb('keywords')->nullable();

            $table->string('author_name')->nullable();
            $table->string('author_email')->nullable();

            $table->string('source_url', 500)->nullable();
            $table->string('composer_url', 500)->nullable();

            $table->string('cover_image', 500)->nullable();
            $table->string('readme_path', 500)->nullable();

            $table->string('source_type', 16)->default(false); // Enum
            $table->boolean('zip_available')->default(false);

            $table->json('composer')->nullable(); // dump completo del composer.json

            $table->string('repository_type', 16)->default('public')->index();
            $table->boolean('active')->default(true);

            // Auditoría
            $table->unsignedMediumInteger('created_by')->nullable()->index();
            $table->unsignedMediumInteger('updated_by')->nullable()->index();
            $table->unsignedMediumInteger('deleted_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            // Indices
            $table->index(['name', 'display_name']);
            $table->index(['repository_type', 'active']);

            // Relaciones
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
        Schema::dropIfExists('module_packages');
    }
};
