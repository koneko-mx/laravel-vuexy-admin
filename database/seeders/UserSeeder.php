<?php

namespace Database\Seeders;

use Koneko\VuexyAdmin\Models\User;
use Koneko\VuexyAdmin\Services\AvatarImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define el disco y la carpeta
        $disk = 'public';
        $directory = 'profile-photos';

        // Verifica si la carpeta existe
        if (Storage::disk($disk)->exists($directory))
            Storage::disk($disk)->deleteDirectory($directory);

        //
        $avatarImageService = new AvatarImageService();

            // Super admin
        $user = User::create([
            'name' => 'Koneko Admin',
            'email' => 'sadmin@koneko.mx',
            'email_verified_at' => now(),
            'password' => bcrypt('LAdmin123'),
            'status' => User::STATUS_ENABLED,
        ])->assignRole('SuperAdmin');

        // Actualizamos la foto
        $avatarImageService->updateProfilePhoto($user, new UploadedFile(
            'public/vendor/vuexy-admin/img/logo/koneko-02.png',
            'koneko-02.png'
        ));


        // admin
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@koneko.mx',
            'email_verified_at' => now(),
            'password' => bcrypt('LAdmin123'),
            'status' => User::STATUS_ENABLED,
        ])->assignRole('Admin');

        $avatarImageService->updateProfilePhoto($user, new UploadedFile(
            'public/vendor/vuexy-admin/img/logo/koneko-03.png',
            'koneko-03.png'
        ));

        // Auditor
        $user = User::create([
            'name' => 'Auditor',
            'email' => 'auditor@koneko.mx',
            'email_verified_at' => now(),
            'password' => bcrypt('LAdmin123'),
            'status' => User::STATUS_ENABLED,
        ])->assignRole('Auditor');

        $avatarImageService->updateProfilePhoto($user, new UploadedFile(
            'public/vendor/vuexy-admin/img/logo/koneko-03.png',
            'koneko-03.png'
        ));


        // Usuarios CSV
        $csvFile = fopen(base_path("database/data/users.csv"), "r");

        $firstline = true;

        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {
                User::create([
                    'name' => $data['0'],
                    'email' => $data['1'],
                    'email_verified_at' => now(),
                    'password' => bcrypt($data['3']),
                    'status' => User::STATUS_ENABLED,
                ])->assignRole($data['2']);
            }

            $firstline = false;
        }

        fclose($csvFile);
    }
}
