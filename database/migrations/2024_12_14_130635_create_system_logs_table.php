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
        Schema::create('system_logs', function (Blueprint $table) {
            $table->integerIncrements('id');

            // Relación polimórfica: puede ser un pedido, una factura, etc.
            $table->unsignedMediumInteger('loggable_id')->index();
            $table->string('loggable_type')->index();

            $table->string('module')->nullable()->index();      // Modulo (opcional pero recomendable)
            $table->unsignedMediumInteger('user_id')->nullable()->index();  // Usuario

            $table->string('level', 16)->index();              // info, warning, error
            $table->text('message');
            $table->json('context')->nullable();  // datos estructurados

            $table->string('trigger_type', 16)->index();       // user, cronjob, webhook, etc.
            $table->unsignedMediumInteger('trigger_id')->nullable()->index(); // user_id, job_id, etc.

            // Auditoría
            $table->unsignedMediumInteger('updated_by')->nullable()->index();
            $table->unsignedMediumInteger('deleted_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['loggable_id', 'loggable_type']);
            $table->index(['loggable_type', 'user_id']);
            $table->index(['loggable_type', 'level']);
            $table->index(['trigger_type', 'level']);
            $table->index(['loggable_type', 'user_id', 'level']);
            $table->index(['trigger_type', 'user_id', 'level']);

            // Relaciones
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
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
        Schema::dropIfExists('system_logs');
    }
};
