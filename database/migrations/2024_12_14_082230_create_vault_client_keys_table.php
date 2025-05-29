<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $keyVault = config_m()->setGroup('key_vault');

        $mode   = $keyVault->get('mode', 'client');
        $driver = $keyVault->get('client.driver', 'database');
        $table  = $keyVault->get('server.table', 'vault_keys');
        $conn   = $keyVault->get('server.connection', 'vault');

        $isServer = in_array($mode, ['server', 'both']);
        $isDatabaseDriver = $driver === 'database';

        if (!$isServer) {
            echo "\n   ⏩ Skipping: vault_keys not needed for mode '{$mode}'.";
            return;
        }

        if (!$isDatabaseDriver) {
            echo "\n   ⏩ Skipping: vault_keys not needed for driver '{$driver}'.";
            return;
        }

        if (Schema::connection($conn)->hasTable($table)) {
            echo "\n   ⏩ Table {$table} already exists on connection '{$conn}'.";
            return;
        }

        // Crear la tabla
        Schema::connection($conn)->create($table, function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('alias', 64)->unique()->index();
            $table->string('owner_project', 64)->index();
            $table->string('namespace', 32)->default('core')->index();

            $table->string('algorithm', 32)->default('AES-256-CBC');
            $table->binary('key_material');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_sensitive')->default(true);

            $table->timestamp('rotated_at')->nullable();
            $table->unsignedInteger('rotation_count')->default(0);

            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->unsignedBigInteger('deleted_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['owner_project', 'namespace', 'is_active'], 'idx_full_context');
        });

        info("✅ Created table `{$table}` in connection [{$conn}].");
    }

    public function down(): void
    {
        $keyVault = config_m()->setGroup('key_vault');

        $driver = $keyVault->get('client.driver', 'database');
        $table  = $keyVault->get('server.table', 'vault_keys');
        $conn   = $keyVault->get('server.connection', 'vault');

        if ($driver === 'database' && Schema::connection($conn)->hasTable($table)) {
            Schema::connection($conn)->dropIfExists($table);
            info("🗑️ Dropped table `{$table}` from connection [{$conn}].");
        }
    }
};
