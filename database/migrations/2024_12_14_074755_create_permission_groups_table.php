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
        Schema::create('permission_groups', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->string("module_register")->index();                         // Nombre del módulo registrado
            $table->unsignedSmallInteger('parent_id')->nullable()->index();     // ID del grupo padre

            $table->string('type', 16)->default('root_group');                  // Enum: root_group, sub_group, external_group
            $table->string("module", 32)->index();
            $table->string("grupo", 32)->nullable()->index();
            $table->string("sub_grupo", 32)->nullable()->index();

            $table->json('name')->nullable();                                   // Nombre i18n
            $table->json('ui_metadata')->nullable();                            // icon, description i18n, flags, ...

            $table->string('priority', 16)->nullable()->index();

            // Auditoría
            $table->timestamps();

            // Relaciones
            $table->foreign('parent_id')->references('id')->on('permission_groups')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_groups');
    }
};
