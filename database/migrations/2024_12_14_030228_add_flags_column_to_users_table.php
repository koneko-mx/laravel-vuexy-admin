<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Koneko\VuexyAdmin\Application\Enums\User\UserBaseFlags;
use Koneko\VuexyAdmin\Support\Traits\Migrations\HandlesGeneratedColumns;

return new class extends Migration
{
    use HandlesGeneratedColumns;

    public function up()
    {
        // Añadir columna contenedora si no existe
        if (!Schema::hasColumn('users', 'flags')) {
            Schema::table('users', function ($table) {
                $table->json('flags')
                    ->nullable()
                    ->after('profile_photo_path')
                    ->comment('Dynamic flags storage');
            });
        }

        // Añadir columnas generadas
        $this->addGeneratedColumns('users', UserBaseFlags::cases());
    }

    public function down()
    {
        // Eliminar columnas generadas
        $this->dropGeneratedColumns('users', UserBaseFlags::cases());

        // Eliminar columna flags (opcional)
        Schema::table('users', function ($table) {
            $table->dropColumnIfExists('flags');
        });
    }
};
