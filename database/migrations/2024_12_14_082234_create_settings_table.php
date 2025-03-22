<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->smallIncrements('id');

            // Clave del setting
            $table->string('key')->index();

            // Categoría (opcional pero recomendable)
            $table->string('category')->nullable()->index();

            // Usuario (null para globales)
            $table->unsignedMediumInteger('user_id')->nullable()->index();

            // Valores segmentados por tipo para mejor rendimiento
            $table->string('value_string')->nullable();
            $table->integer('value_integer')->nullable();
            $table->boolean('value_boolean')->nullable();
            $table->float('value_float', 16, 8)->nullable();
            $table->text('value_text')->nullable();
            $table->binary('value_binary')->nullable();
            $table->string('mime_type', 50)->nullable();
            $table->string('file_name')->nullable();

            // Auditoría
            $table->timestamps();
            $table->unsignedMediumInteger('updated_by')->nullable();

            // Unique constraint para evitar duplicados
            $table->unique(['key', 'user_id', 'category']);

            // Relaciones
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        // Agregar columna virtual unificada
        DB::statement("ALTER TABLE settings ADD COLUMN value VARCHAR(255) GENERATED ALWAYS AS (
            CASE
                WHEN value_string IS NOT NULL THEN value_string
                WHEN value_integer IS NOT NULL THEN CAST(value_integer AS CHAR)
                WHEN value_boolean IS NOT NULL THEN IF(value_boolean, 'true', 'false')
                WHEN value_float IS NOT NULL THEN CAST(value_float AS CHAR)
                WHEN value_text IS NOT NULL THEN LEFT(value_text, 255)
                WHEN value_binary IS NOT NULL THEN '[binary_data]'
                ELSE NULL
            END
        ) VIRTUAL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }

};
