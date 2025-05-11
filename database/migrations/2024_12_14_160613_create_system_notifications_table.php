<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration  {
    public function up(): void
    {
        Schema::create('system_notifications', function (Blueprint $table) {
            $table->mediumIncrements('id');

            $table->string('scope', 16)->default('both')->index();   // enum 'admin', 'frontend', 'both'
            $table->string('type', 16)->default('info')->index();    // enum 'info', 'success', 'warning', 'danger', 'promo'

            $table->string('title')->index();
            $table->text('message');

            $table->string('channel', 32)->default('toast')->index();       // toast | push | websocket | etc.

            $table->string('style', 16)->default('banner')->index();        // enum 'toast', 'banner', 'modal', 'inline'
            $table->string('priority', 16)->default('medium')->index();     // Enum 'low', 'medium', 'high', 'critical'

            $table->boolean('requires_confirmation')->default(false)->index();

            $table->string('target_area', 32)->nullable()->comment('Ej: header, sidebar, checkout, etc.')->index();
            $table->json('tags')->nullable();
            $table->json('roles')->nullable();
            $table->json('user_flags')->nullable();

            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            // Auditoría
            $table->unsignedMediumInteger('created_by')->nullable()->index();
            $table->unsignedMediumInteger('updated_by')->nullable()->index();
            $table->unsignedMediumInteger('deleted_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            // Indices
            $table->index(['scope', 'type']);
            $table->index(['is_active', 'starts_at', 'ends_at']);

            // Relaciones
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('deleted_by')->references('id')->on('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_notifications');
    }
};
