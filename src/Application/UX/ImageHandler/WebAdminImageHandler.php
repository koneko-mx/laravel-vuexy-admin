<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UX\ImageHandler;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

/**
 * Servicio para gestionar favicon y logos administrativos.
 */
class WebAdminImageHandler
{
    private string $driver;
    private string $imageDisk = 'public';

    public const FAVICON_BASE_PATH = 'favicon-admin/';
    public const LOGO_BASE_PATH    = 'logo-admin/';

    private string $group   = 'layout';
    private string $section = 'admin';


    public function __construct()
    {
        $this->driver = config('image.driver', 'gd');
    }

    /**
     * Procesa y guarda múltiples versiones del favicon.
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @return void
     */
    public function processAndSaveFavicon(\Illuminate\Http\UploadedFile $image): void
    {
        ///Storage::makeDirectory(self::FAVICON_BASE_PATH);

        $currentNamespace = settings('core')
            ->context($this->group, $this->section, 'favicon')
            ->get('favicon_ns');

        if ($currentNamespace) {
            $this->deleteOldFiles($this->generateFaviconPaths($currentNamespace));
        }

        $imageManager = new ImageManager($this->driver);
        $baseName = uniqid('favicon_', true);

        foreach ($this->getFaviconSizes() as $size => [$w, $h]) {
            $resized = $imageManager->read($image->getRealPath())->cover($w, $h);
            Storage::disk($this->imageDisk)
                ->put(self::FAVICON_BASE_PATH . "{$baseName}_{$size}.png", $resized->toPng(indexed: true));
        }

        settings('core')
            ->context($this->group, $this->section, 'favicon')
            ->set('favicon_ns', $baseName);
    }

    /**
     * Procesa y guarda versiones de imagen de logo.
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @param string $type
     * @return void
     */
    public function processAndSaveImageLogo(\Illuminate\Http\UploadedFile $image, string $type = ''): void
    {
        Storage::makeDirectory(self::LOGO_BASE_PATH);

        $this->deleteOldLogoImages($type);

        $imageManager = new ImageManager($this->driver);
        $original = $imageManager->read($image->getRealPath());

        $this->saveResizedLogo($original, 22500, 'small', $type);
        $this->saveResizedLogo($original, 75625, 'medium', $type);
        $this->saveResizedLogo($original, 262144, '', $type);
        $this->saveBase64Logo($original, 230400, $type);
    }

    /**
     * Redimensiona y guarda un logo.
     */
    private function saveResizedLogo($image, int $maxPixels, string $suffix = '', string $type = ''): void
    {
        $suffix = $suffix ? "_{$suffix}" : '';
        $type   = $type ? "_{$type}" : '';

        $resized = clone $image;
        $this->resizeImageToMaxPixels($resized, $maxPixels);

        $fileName = uniqid("logo{$suffix}{$type}_", true) . '.png';
        $path = self::LOGO_BASE_PATH . $fileName;

        Storage::disk($this->imageDisk)
            ->put($path, $resized->toPng(indexed: true));

        $keyName = "image_logo{$suffix}{$type}";

        settings('core')
            ->context($this->group, $this->section, "logo{$type}")
            ->set($keyName, $fileName);
    }

    /**
     * Guarda un logo en formato base64.
     */
    private function saveBase64Logo($image, int $maxPixels, string $type = ''): void
    {
        $resized = clone $image;
        $this->resizeImageToMaxPixels($resized, $maxPixels);

        $base64 = (string) $resized->toJpg(40)->toDataUri();

        $type = $type ? "_{$type}" : '';
        $keyName = "image_logo_base64{$type}";

        settings('core')
            ->context($this->group, $this->section, "logo{$type}")
            ->set($keyName, $base64);
    }

    /**
     * Elimina archivos de imágenes antiguos.
     */
    private function deleteOldFiles(array $paths): void
    {
        foreach ($paths as $path) {
            if (Storage::disk($this->imageDisk)->exists($path)) {
                Storage::disk($this->imageDisk)->delete($path);
            }
        }
    }

    /**
     * Elimina versiones anteriores de logos.
     */
    private function deleteOldLogoImages(string $type = ''): void
    {
        $type = $type ? "_{$type}" : '';

        $keys = [
            "image_logo{$type}",
            "image_logo_small{$type}",
            "image_logo_medium{$type}",
        ];

        $paths = [];

        foreach ($keys as $key) {
            $path = settings('core')
                ->context($this->group, $this->section, "logo{$type}")
                ->get($key);

            if ($path) {
                $paths[] = $path;
            }
        }

        $this->deleteOldFiles($paths);
    }

    /**
     * Redimensiona imagen conservando aspecto.
     */
    private function resizeImageToMaxPixels($image, int $maxPixels)
    {
        $originalWidth = $image->width();
        $originalHeight = $image->height();
        $aspectRatio = $originalWidth / $originalHeight;

        if ($aspectRatio > 1) {
            $newWidth = sqrt($maxPixels * $aspectRatio);
            $newHeight = $newWidth / $aspectRatio;

        } else {
            $newHeight = sqrt($maxPixels / $aspectRatio);
            $newWidth = $newHeight * $aspectRatio;
        }

        $image->resize(
            (int) round($newWidth),
            (int) round($newHeight),
            function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            }
        );

        return $image;
    }

    /**
     * Obtiene los tamaños estándar para favicons.
     */
    private function getFaviconSizes(): array
    {
        return [
            '16x16' => [16, 16],
            '76x76' => [76, 76],
            '120x120' => [120, 120],
            '152x152' => [152, 152],
            '180x180' => [180, 180],
            '192x192' => [192, 192],
        ];
    }

    /**
     * Genera las rutas de favicons a eliminar.
     */
    private function generateFaviconPaths(string $base): array
    {
        return array_map(fn($size) => self::FAVICON_BASE_PATH . "{$base}_{$size}.png", array_keys($this->getFaviconSizes()));
    }
}
