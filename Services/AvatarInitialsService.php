<?php

namespace Koneko\VuexyAdmin\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Typography\FontFactory;

class AvatarInitialsService
{
    protected $avatarDisk = 'public';
    protected $initialAvatarDir = 'initial-avatars';
    protected $avatarWidth = 512;
    protected $avatarHeight = 512;
    protected const INITIAL_MAX_LENGTH = 3;
    protected const AVATAR_BACKGROUND = '#EBF4FF';
    protected const AVATAR_COLORS = [
        '#3b82f6',
        '#808390',
        '#28c76f',
        '#ff4c51',
        '#ff9f43',
        '#00bad1',
        '#4b4b4b',
    ];

    /**
     * Genera o retorna el avatar basado en las iniciales.
     *
     * @param string $name Nombre completo del usuario.
     *
     * @return \Illuminate\Http\Response Respuesta con la imagen generada.
     */
    public function getAvatarImage($name)
    {
        $color       = $this->getAvatarColor($name);
        $background  = ltrim(self::AVATAR_BACKGROUND, '#');
        $size        = ($this->avatarWidth + $this->avatarHeight) / 2;
        $initials    = self::getInitials($name);
        $cacheKey    = "avatar-{$initials}-{$color}-{$background}-{$size}";
        $path        = "{$this->initialAvatarDir}/{$cacheKey}.png";
        $storagePath = storage_path("app/public/{$path}");

        if (Storage::disk($this->avatarDisk)->exists($path)) {
            return response()->file($storagePath);
        }

        $image = $this->createAvatarImage($name, $color, self::AVATAR_BACKGROUND, $size);
        Storage::disk($this->avatarDisk)->put($path, $image->toPng(indexed: true));

        return response()->file($storagePath);
    }

    /**
     * Crea la imagen del avatar con las iniciales.
     *
     * @param string $name Nombre completo.
     * @param string $color Color del texto.
     * @param string $background Color de fondo.
     * @param int $size Tamaño de la imagen.
     *
     * @return \Intervention\Image\Image La imagen generada.
     */
    protected function createAvatarImage($name, $color, $background, $size)
    {
        $driver = config('image.driver', 'gd');
        $manager = new ImageManager($driver);
        $initials = self::getInitials($name);
        $fontPath = __DIR__ . '/../storage/fonts/OpenSans-Bold.ttf';

        $image = $manager->create($size, $size)
            ->fill($background);

        $image->text(
            $initials,
            $size / 2,
            $size / 2,
            function (FontFactory $font) use ($color, $size, $fontPath) {
                $font->file($fontPath);
                $font->size($size * 0.4);
                $font->color($color);
                $font->align('center');
                $font->valign('middle');
            }
        );

        return $image;
    }

    /**
     * Calcula las iniciales a partir del nombre.
     *
     * @param string $name Nombre completo.
     *
     * @return string Iniciales en mayúsculas.
     */
    public static function getInitials($name)
    {
        if (empty($name)) {
            return 'NA';
        }

        $initials = implode('', array_map(function ($word) {
            return mb_substr($word, 0, 1);
        }, explode(' ', $name)));

        return strtoupper(substr($initials, 0, self::INITIAL_MAX_LENGTH));
    }

    /**
     * Selecciona un color basado en el nombre.
     *
     * @param string $name Nombre del usuario.
     *
     * @return string Color seleccionado.
     */
    public function getAvatarColor($name)
    {
        // Por ejemplo, se puede basar en la suma de los códigos ASCII de las letras del nombre
        $hash = array_sum(array_map('ord', str_split($name)));

        return self::AVATAR_COLORS[$hash % count(self::AVATAR_COLORS)];
    }
}
