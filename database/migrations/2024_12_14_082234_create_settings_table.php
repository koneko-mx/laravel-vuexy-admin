<?php

namespace Koneko\VuexyAdmin\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->integerIncrements('id');

            // Clave identificadora única para uso directo en Redis u otras estructuras rápidas
            $table->string('key', 198)->charset('ascii')->collation('ascii_bin')->unique();

            // Contexto completo del setting
            $table->string('namespace', 8)->index();                         // Ej. 'koneko'
            $table->string('environment', 10)->default('production')->index();      // production, development, staging, test
            $table->string('component', 16)->index();                        // Ej. 'vuexy-admin'
            $table->string('module')->nullable()->index();                   // composerName del módulo

            $table->string('scope', 16)->nullable()->index();                // user, branch, tenant, etc.
            $table->unsignedMediumInteger('scope_id')->nullable()->index();  // ID vinculado al scope (ej. user_id, branch_id)

            $table->string('group', 16)->index();                            // Grupo funcional (ej. layout, ui, behavior)
            $table->string('section', 16)->default('default')->index();      // Bloque intermedio semántico
            $table->string('sub_group', 16)->default('default')->index();    // Subgrupo semántico opcional
            $table->string('key_name', 24)->index();     // Sub-subgrupo lógico o tipo de configuración

            // Flags operativos
            $table->boolean('is_system')->default(false)->index();
            $table->boolean('is_sensitive')->default(false)->index();
            $table->boolean('is_file')->default(false)->index();
            $table->boolean('is_encrypted')->default(false)->index();
            $table->boolean('is_config')->default(false)->index();
            $table->boolean('is_editable')->default(true)->index();
            $table->boolean('is_track_usage')->default(false)->index();
            $table->boolean('is_should_cache')->default(true)->index();
            $table->boolean('is_active')->default(true)->index();

            // Metadata para archivos y cifrado
            $table->string('mime_type', 50)->nullable();
            $table->string('file_name')->nullable();
            $table->string('encryption_algorithm', 16)->nullable();
            $table->string('encryption_key', 64)->nullable()->index();
            $table->timestamp('encryption_rotated_at')->nullable();

            // Expiración del setting o su valor cacheado
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('usage_count')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->unsignedSmallInteger('cache_ttl')->nullable();
            $table->timestamp('cache_expires_at')->nullable();

            // Descripciones para UI
            $table->string('description')->nullable();
            $table->string('hint')->nullable();

            // Valores segmentados
            $table->string('value_string')->nullable();
            $table->integer('value_integer')->nullable();
            $table->boolean('value_boolean')->nullable();
            $table->float('value_float', 16, 8)->nullable();
            $table->text('value_text')->nullable();
            $table->binary('value_binary')->nullable();

            // Auditoría
            $table->unsignedMediumInteger('created_by')->nullable()->index();
            $table->unsignedMediumInteger('updated_by')->nullable()->index();
            $table->unsignedMediumInteger('deleted_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            // 📌 Clave única por contexto semántico-lógico
            $table->unique([
                'namespace',
                'environment',
                'component',
                'scope',
                'scope_id',
                'group',
                'section',
                'sub_group',
                'key_name'
            ], 'uniq_settings_full_context');

            // 📈 Índices especializados para consultas
            $table->index(['namespace', 'environment', 'component'], 'idx_ns_env_component');
            $table->index(['namespace', 'environment', 'component', 'is_active'], 'idx_ns_is_active');
            $table->index(['namespace', 'environment', 'component', 'is_system'], 'idx_ns_is_system');
            $table->index(['namespace', 'environment', 'component', 'is_config'], 'idx_ns_is_config');
            $table->index(['namespace', 'environment', 'component', 'is_encrypted'], 'idx_ns_is_encrypted');
            $table->index(['namespace', 'environment', 'component', 'is_file'], 'idx_ns_is_file');
            $table->index(['namespace', 'environment', 'component', 'is_editable'], 'idx_ns_is_editable');
            $table->index(['namespace', 'environment', 'component', 'is_track_usage'], 'idx_ns_is_track_usage');
            $table->index(['namespace', 'environment', 'component', 'is_should_cache'], 'idx_ns_is_should_cache');
            $table->index(['namespace', 'environment', 'component', 'group', 'section', 'sub_group'], 'idx_ns_env_comp_group_sect_subg');
            $table->index(['namespace', 'environment', 'component', 'group', 'section', 'sub_group', 'scope', 'scope_id'], 'idx_ns_env_comp_group_sect_subg_scope');

            // 🔒 Relaciones de auditoría
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('deleted_by')->references('id')->on('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
