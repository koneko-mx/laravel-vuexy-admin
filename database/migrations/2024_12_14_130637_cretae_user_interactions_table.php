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
        Schema::create('user_interactions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('module')->nullable()->index();      // Modulo (opcional pero recomendable)
            $table->unsignedMediumInteger('user_id')->index();  // Usuario

            $table->string('livewire_component')->nullable()->index();      // ejemplo: inventory.products
            $table->string('action')->index();                              // ejemplo: view, update, export
            $table->string('security_level')->default('normal')->index();   // normal, sensible, crítico

            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('context')->nullable();

            // 🛡️ Flags de auditoría administrativa
            $table->json('user_flags')->nullable();     // snapshot de is_admin, is_client, etc.
            $table->json('user_roles')->nullable();     // ["admin", "manager"]

            $table->mediumText('notes')->nullable();    // comentarios internos de admin
            $table->json('chat_thread')->nullable();  // [{user_id:1, msg:"...", at:"..."}, ...]

            $table->boolean('is_reviewed')->default(false)->index();
            $table->boolean('is_flagged')->default(false)->index();
            $table->boolean('is_escalated')->default(false)->index();
            $table->unsignedMediumInteger('reviewed_by')->nullable()->index();
            $table->unsignedMediumInteger('flagged_by')->nullable()->index();
            $table->unsignedMediumInteger('escalated_by')->nullable()->index();

            // Auditoria
            $table->unsignedMediumInteger('updated_by')->nullable()->index();
            $table->unsignedMediumInteger('deleted_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            // Indices
            $table->index(['user_id', 'is_reviewed', 'security_level']);
            $table->index(['user_id', 'is_flagged', 'security_level']);
            $table->index(['user_id', 'is_escalated', 'security_level']);
            $table->index(['user_id', 'is_reviewed', 'is_flagged', 'is_escalated', 'security_level'])->name('user_interactions_user_id_is_reviewed_flagged_escalated_sec_lev_index');

            // Relaciones
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('flagged_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('escalated_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_interactions');
    }
};
