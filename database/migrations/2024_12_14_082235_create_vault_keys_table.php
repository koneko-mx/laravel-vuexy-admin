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
        Schema::connection('vault')->create('vault_keys', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Contexto de clave
            $table->string('alias', 64)->unique()->index();
            $table->string('owner_project', 64)->index();
            $table->string('environment', 10)->default('prod')->index(); // prod, dev, staging
            $table->string('namespace', 32)->default('core')->index();
            $table->string('scope', 16)->default('global')->index();     // global, tenant, user

            // Datos de la clave
            $table->string('algorithm', 32)->default('AES-256-CBC');
            $table->binary('key_material');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_sensitive')->default(true);

            // Control de rotación
            $table->timestamp('rotated_at')->nullable();
            $table->unsignedInteger('rotation_count')->default(0);

            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->unsignedBigInteger('deleted_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            // Índices adicionales
            $table->index(['owner_project', 'environment', 'namespace', 'scope', 'is_active'], 'idx_full_context');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('vault')->dropIfExists('settings');
    }

};
