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
        Schema::create('settings', function (Blueprint $table) {
            $table->mediumIncrements('id');

            $table->string('key')->index();
            $table->text('value');
            $table->unsignedMediumInteger('user_id')->nullable()->index();

            // Unique constraints
            $table->unique(['user_id', 'key']);

            // Relaciones
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }

};
