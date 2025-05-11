<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();

            $table->unsignedMediumInteger('user_id')->nullable()->index();
            $table->string('token')->unique(); // Token del dispositivo
            $table->string('platform', 32)->nullable()->index(); // Plataforma: ios, android, web, desktop, etc.
            $table->string('client')->nullable(); // Navegador o cliente usado
            $table->mediumText('device_info')->nullable(); // Información extendida del dispositivo
            $table->string('location', 128)->nullable(); // Ubicación del dispositivo (opcional)
            $table->timestamp('last_used_at')->nullable(); // Último uso

            $table->boolean('is_active')->default(true);

            // Auditoria
            $table->unsignedMediumInteger('created_by')->nullable()->index();
            $table->unsignedMediumInteger('updated_by')->nullable()->index();
            $table->unsignedMediumInteger('deleted_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            // Indices
            $table->index(['user_id', 'platform']);
            $table->index(['user_id', 'is_active']);
            $table->index(['user_id', 'is_active', 'platform']);

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
        Schema::dropIfExists('device_tokens');
    }
};
