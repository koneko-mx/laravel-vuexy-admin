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
        Schema::create('user_logins', function (Blueprint $table) {
            $table->integerIncrements('id');

            $table->unsignedMediumInteger('user_id')->nullable()->index();

            $table->ipAddress('ip_address')->nullable()->index();
            $table->string('user_agent')->nullable();
            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('browser_version')->nullable();
            $table->string('os')->nullable();
            $table->string('os_version')->nullable();
            $table->string('country')->nullable()->index();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->boolean('is_proxy')->default(false)->index();
            $table->boolean('login_success')->default(true)->index();
            $table->timestamp('logout_at')->nullable();
            $table->string('logout_reason')->nullable();
            $table->json('additional_info')->nullable();

            // Auditoría
            $table->timestamps();

            // Indices
            $table->index(['user_id', 'login_success']);
            $table->index(['user_id', 'logout_at']);
            $table->index(['user_id', 'logout_at', 'login_success']);

            // Relaciones
            $table->foreign('user_id')->references('id')->on('users');
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
