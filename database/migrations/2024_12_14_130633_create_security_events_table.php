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
        Schema::create('security_events', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('module')->nullable()->index();                  // Modulo (opcional pero recomendable)
            $table->unsignedMediumInteger('user_id')->nullable()->index();  // Usuario

            // Información básica del evento
            $table->string('event_type')->index();
            $table->string('status')->default('new')->index();

            // Información de acceso
            $table->ipAddress('ip_address')->nullable()->index();
            $table->string('user_agent')->nullable();
            $table->string('device_type', 100)->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('browser_version')->nullable();
            $table->string('os', 100)->nullable();
            $table->string('os_version', 100)->nullable();

            // Información GeoIP (geoip2)
            $table->string('country')->nullable()->index();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();

            // Información adicional del evento
            $table->boolean('is_proxy')->default(false)->index();
            $table->string('url')->nullable();
            $table->string('http_method', 10)->nullable();

            // JSON payload del evento para análisis avanzado
            $table->json('payload')->nullable();

            // Auditoría
            $table->unsignedMediumInteger('deleted_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            // Indices
            $table->index(['event_type', 'status', 'user_id']);
            $table->index(['user_id', 'event_type']);
            $table->index(['user_id', 'event_type', 'status']);

            // Relaciones
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Elimina tablas solo si existen
        Schema::dropIfExists('user_logins');
    }
};
