<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB,Schema};

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE `users` MODIFY `id` BIGINT UNSIGNED NOT NULL;');
        DB::statement('ALTER TABLE `users` DROP PRIMARY KEY;');
        DB::statement('ALTER TABLE `users` MODIFY `id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);');

        Schema::table('users', function (Blueprint $table) {
            $table->string('last_name', 100)->nullable()->comment('Apellidos')->index()->after('name');
            $table->string('profile_photo_path', 2048)->nullable()->after('remember_token');
            $table->boolean('status')->default(1)->after('profile_photo_path');
            $table->unsignedMediumInteger('created_by')->nullable()->index()->after('status');

            // Auditoria
            $table->softDeletes();

            // Definir la relación con created_by
            $table->foreign('created_by')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE `users` MODIFY `id` MEDIUMINT UNSIGNED NOT NULL;');
        DB::statement('ALTER TABLE `users` DROP PRIMARY KEY;');
        DB::statement('ALTER TABLE `users` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);');
    }
};
