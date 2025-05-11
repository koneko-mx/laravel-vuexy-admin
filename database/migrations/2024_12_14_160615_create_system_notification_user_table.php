<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration  {
    public function up(): void
    {
        Schema::create('system_notification_user', function (Blueprint $table) {
            $table->id();

            $table->unsignedMediumInteger('system_notification_id')->index();
            $table->unsignedMediumInteger('user_id')->index();

            $table->string('channel', 32)->default('toast')->index();       // toast | push | websocket | etc.

            $table->boolean('is_read')->default(false)->index();
            $table->timestamp('read_at')->nullable()->index();

            $table->boolean('is_dismissed')->default(false)->index();
            $table->timestamp('dismissed_at')->nullable()->index();

            $table->boolean('is_confirmed')->default(false)->comment('Confirmación explícita si se requiere')->index();
            $table->timestamp('confirmed_at')->nullable()->index();
            $table->text('confirmation_notes')->nullable();

            // Auditoria
            $table->timestamps();

            // Indices
            $table->unique(['system_notification_id', 'user_id']);

            // Relaciones
            $table->foreign('system_notification_id')->references('id')->on('system_notifications')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_notification_user');
    }
};
