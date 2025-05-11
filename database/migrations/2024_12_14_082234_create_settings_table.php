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
            $table->mediumIncrements('id');

            $table->string('key')->index();         // Clave del setting

            $table->string('namespace', 8)->index();                       // Namespace del setting
            $table->string('environment', 7)->default('prod')->index();    // Entorno de aplicación (prod, dev, test, staging), permite sobrescribir valores según ambiente.
            $table->string('scope', 6)->default('global')->index();        // Define el alcance: global, tenant, branch, user, etc. Útil en arquitecturas multicliente.

            $table->string('component', 16)->index();                       // Nombre de Componente o proyecto
            $table->string('module')->nullable()->index();                  // composerName de módulo Autocalculado
            $table->string('group', 16)->index();                           // Grupo de configuraciones
            $table->string('sub_group', 16)->index();                       // Sub grupo de configuraciones
            $table->string('key_name', 24)->index();                        // Nombre de la clave de configuraciones
            $table->unsignedMediumInteger('user_id')->nullable()->index();  // Usuario (null para globales)

            $table->boolean('is_system')->default(false)->index();          // Indica si es un setting de sistema
            $table->boolean('is_encrypted')->default(false)->index();       // Si el valor está cifrado (para secretos, tokens, passwords).
            $table->boolean('is_sensitive')->default(false)->index();       // Marca datos sensibles (ej. datos personales, claves API). Puede ocultarse en UI o logs.
            $table->boolean('is_editable')->default(true)->index();         // Permite o bloquea edición desde la UI (útil para settings de solo lectura).
            $table->boolean('is_active')->default(true)->index();           // Permite activar/desactivar la aplicación de un setting sin eliminarlo.

            $table->string('encryption_key', 64)->nullable()->index();      // Identificador de la clave usada (ej. 'ssl_cert_2025')
            $table->string('encryption_algorithm', 16)->nullable();         // Ej. 'AES-256-CBC'
            $table->timestamp('encryption_rotated_at')->nullable();         // Fecha de última rotación de clave

            $table->string('description')->nullable();
            $table->string('hint')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->integer('usage_count')->default(0)->index();

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
            $table->unsignedMediumInteger('created_by')->nullable()->index();
            $table->unsignedMediumInteger('updated_by')->nullable()->index();
            $table->unsignedMediumInteger('deleted_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes(); // THIS ONE

            //  Índice de Unicidad Principal (para consultas y updates rápidos)
            $table->unique(
                ['namespace', 'environment', 'scope', 'component', 'group', 'sub_group', 'key_name', 'user_id'],
                'uniq_settings_full_context'
            );

            // Búsqueda rápida por componente
            $table->index(['namespace', 'component'], 'idx_settings_ns_component');

            // Listar grupos de un componente por scope
            $table->index(['namespace', 'scope', 'component', 'group'], 'idx_settings_ns_scope_component_group');

            // Listar subgrupos por grupo y componente en un scope
            $table->index(['namespace', 'scope', 'component', 'group', 'sub_group'], 'idx_settings_ns_scope_component_sg');

            // Consultas por entorno y usuario
            $table->index(['namespace', 'environment', 'user_id'], 'idx_settings_ns_env_user');

            // Consultas por scope y usuario
            $table->index(['namespace', 'scope', 'user_id'], 'idx_settings_ns_scope_user');

            // Consultas por estado de actividad o sistema
            $table->index(['namespace', 'is_active'], 'idx_settings_ns_is_active');
            $table->index(['namespace', 'is_system'], 'idx_settings_ns_is_system');

            // Consultas por estado de cifrado y usuario
            $table->index(['namespace', 'is_encrypted', 'user_id'], 'idx_settings_encrypted_user');

            // Relaciones
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
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
        Schema::dropIfExists('settings');
    }

};
