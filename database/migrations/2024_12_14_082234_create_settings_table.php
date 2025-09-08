<?php

namespace Koneko\VuexyAdmin\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');

            // Clave identificadora única para uso directo en Redis u otras estructuras rápidas
            $table->string('key', 198)->charset('ascii')->collation('ascii_bin');

            // Contexto completo del setting
            $table->string('namespace', 32)->index();                         // Ej. 'koneko'
            $table->string('environment', 16)->index();      // production, development, staging, test
            $table->string('component', 32)->index();                        // Ej. 'vuexy-website'

            $table->string('scope', 32)->nullable()->index();                // user, branch, tenant, etc.
            $table->unsignedInteger('scope_id')->nullable()->index();        // ID vinculado al scope (ej. user_id, branch_id)

            $table->string('group', 32)->index();                            // Grupo funcional (ej. layout, ui, behavior)
            $table->string('section', 32)->default('default')->index();      // Bloque intermedio semántico
            $table->string('sub_group', 32)->default('default')->index();    // Subgrupo semántico opcional
            $table->string('key_name', 32)->index();                         //

            // Flags operativos
            $table->boolean('is_file')->default(false);
            $table->boolean('is_encrypted')->default(false);
            $table->boolean('is_config')->default(false);
            $table->boolean('is_track_usage')->default(false);
            $table->boolean('is_should_cache')->default(true);
            $table->boolean('is_active')->default(true);

            // Metadata para archivos y cifrado
            $table->string('mime_type', 50)->nullable();
            $table->string('file_name')->nullable();
            $table->string('encryption_algorithm', 16)->nullable();
            $table->string('encryption_key', 80)->nullable();
            $table->timestamp('encryption_rotated_at')->nullable();

            // Expiración del setting o su valor cacheado
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('usage_count')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->unsignedInteger('cache_ttl')->nullable();
            $table->timestamp('cache_expires_at')->nullable();

            // Descripciones para UI
            $table->string('description')->nullable();
            $table->string('hint')->nullable();

            // Valores segmentados
            $table->string('value_string')->nullable();
            $table->integer('value_integer')->nullable();
            $table->boolean('value_boolean')->nullable();
            $table->double('value_float', 16, 8)->nullable();
            $table->longText('value_text')->nullable();
            $table->binary('value_binary')->nullable();

            // Auditoría
            $table->unsignedMediumInteger('created_by')->nullable()->index();
            $table->unsignedMediumInteger('updated_by')->nullable()->index();

            $table->timestamps();

            // 📌 Clave única por contexto semántico-lógico
            $table->unique('key');
            $table->unique([
                'namespace','environment','component','scope','scope_id',
                'group','section','sub_group','key_name',
              ], 'uniq_settings_full_context');

            // 📈 Índices especializados para consultas
            $table->index(['namespace', 'environment', 'component'], 'idx_ns_env_component');
            $table->index(['namespace', 'environment', 'component', 'group', 'section', 'sub_group'], 'idx_ns_env_comp_group_sect_subg');
            $table->index(['namespace', 'environment', 'component', 'group', 'section', 'sub_group', 'scope', 'scope_id'], 'idx_ns_env_comp_group_sect_subg_scope');

            // 🔒 Relaciones de auditoría
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
