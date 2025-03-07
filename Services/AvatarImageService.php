<?php

namespace Koneko\VuexyAdmin\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Koneko\VuexyAdmin\Models\User;

class AvatarImageService
{
    protected $avatarDisk = 'public';
    protected $profilePhotoDir = 'profile-photos';
    protected $avatarWidth = 512;
    protected $avatarHeight = 512;

    /**
     * Actualiza la foto de perfil procesando la imagen subida.
     *
     * @param mixed $user Objeto usuario que se va a actualizar.
     * @param UploadedFile $image_avatar Archivo de imagen subido.
     *
     * @throws \Exception Si el archivo no existe o tiene un formato inválido.
     *
     * @return void
     */
    public function updateProfilePhoto(User $user, UploadedFile $image_avatar)
    {
        if (!file_exists($image_avatar->getRealPath())) {
            throw new \Exception('El archivo no existe en la ruta especificada.');
        }

        if (!in_array($image_avatar->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
            throw new \Exception('El formato del archivo debe ser JPG o PNG.');
        }

        $avatarName = uniqid('avatar_') . '.png';
        $driver     = config('image.driver', 'gd');

        $manager = new ImageManager($driver);

        if (!Storage::disk($this->avatarDisk)->exists($this->profilePhotoDir)) {
            Storage::disk($this->avatarDisk)->makeDirectory($this->profilePhotoDir);
        }

        $image = $manager->read($image_avatar->getRealPath());
        $image->cover($this->avatarWidth, $this->avatarHeight);
        Storage::disk($this->avatarDisk)->put($this->profilePhotoDir . '/' . $avatarName, $image->toPng(indexed: true));

        // Eliminar avatar existente
        $this->deleteProfilePhoto($user);

        $user->forceFill([
            'profile_photo_path' => $avatarName,
        ])->save();
    }


    /**
     * Elimina la foto de perfil actual del usuario.
     *
     * @param mixed $user Objeto usuario.
     *
     * @return void
     */
    public function deleteProfilePhoto($user)
    {
        if (!empty($user->profile_photo_path)) {
            Storage::disk($this->avatarDisk)->delete($user->profile_photo_path);

            $user->forceFill([
                'profile_photo_path' => null,
            ])->save();
        }
    }
}
