<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Koneko\VuexyAdmin\Services\RBACService;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        RBACService::loadRolesAndPermissions();
    }
}
