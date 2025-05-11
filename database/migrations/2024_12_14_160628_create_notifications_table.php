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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->string('module')->nullable()->index();      // Modulo (opcional pero recomendable)
            $table->unsignedMediumInteger('user_id')->index();  // Usuario

            $table->string('channel', 32)->default('toast')->index();       // toast | push | websocket | etc.

            $table->string('type', 16)->default('info')->index(); // Enum: info, success, danger, warning, system
            $table->string('title')->index();
            $table->text('body')->nullable();
            $table->json('data')->nullable();
            $table->string('action_url')->nullable();

            $table->boolean('is_read')->default(false)->index();
            $table->timestamp('read_at')->nullable();

            // Auditoria
            $table->timestamps();
            $table->boolean('is_dismissed')->default(false)->index();
            $table->boolean('is_deleted')->default(false)->index();
            $table->unsignedMediumInteger('emitted_by')->nullable()->index();

            $table->softDeletes();

            // Indices
            $table->index(['type', 'title']);
            $table->index(['is_read', 'user_id']);
            $table->index(['user_id', 'is_read', 'is_deleted']);

            // Relaciones
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('emitted_by')->references('id')->on('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
