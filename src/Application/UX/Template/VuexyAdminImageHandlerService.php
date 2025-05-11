<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\System;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Koneko\VuexyAdmin\Application\Contracts\Settings\SettingsRepositoryInterface;

/**
 * Servicio para gestionar favicon y logos administrativos.
 */
class VuexyAdminImageHandlerService
{
    private string $driver;
    private string $imageDisk = 'public';
    private string $faviconBasePath = 'favicon/';
    private string $logoBasePath = 'images/logo/';

    private SettingsRepositoryInterface $settings;

    /**
     * Constructor.
     */
    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->driver = config('image.driver', 'gd');
        $this->settings = $settings->self();
    }

    /**
     * Procesa y guarda múltiples versiones del favicon.
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @return void
     */
    public function processAndSaveFavicon(\Illuminate\Http\UploadedFile $image): void
    {
        Storage::makeDirectory("{$this->imageDisk}/{$this->faviconBasePath}");

        $currentNamespace = $this->settings->get('favicon_ns');
        if ($currentNamespace) {
            $this->deleteOldFiles($this->generateFaviconPaths($currentNamespace));
        }

        $imageManager = new ImageManager($this->driver);
        $baseName = uniqid('favicon_', true);

        foreach ($this->getFaviconSizes() as $size => [$w, $h]) {
            $resized = $imageManager->read($image->getRealPath())->cover($w, $h);
            Storage::disk($this->imageDisk)
                ->put("{$this->faviconBasePath}{$baseName}_{$size}.png", $resized->toPng(indexed: true));
        }

        $this->settings->set('favicon_ns', "{$this->faviconBasePath}{$baseName}");
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
        Storage::makeDirectory("{$this->imageDisk}/{$this->logoBasePath}");

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
        $resized = clone $image;
        $this->resizeImageToMaxPixels($resized, $maxPixels);

        $fileName = uniqid("logo_{$suffix}{$type}", true) . '.png';
        $path = "{$this->logoBasePath}{$fileName}";

        Storage::disk($this->imageDisk)->put($path, $resized->toPng(indexed: true));

        $this->settings->set("image_logo" . ($suffix ? "_{$suffix}" : '') . ($type ? "_{$type}" : ''), $path);
    }

    /**
     * Guarda un logo en formato base64.
     */
    private function saveBase64Logo($image, int $maxPixels, string $type = ''): void
    {
        $resized = clone $image;
        $this->resizeImageToMaxPixels($resized, $maxPixels);

        $base64 = (string) $resized->toJpg(40)->toDataUri();

        $this->settings->set("image_logo_base64" . ($type ? "_{$type}" : ''), $base64);
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
        $keys = [
            "image_logo" . ($type ? "_{$type}" : ''),
            "image_logo_small" . ($type ? "_{$type}" : ''),
            "image_logo_medium" . ($type ? "_{$type}" : ''),
        ];

        $paths = [];

        foreach ($keys as $key) {
            $path = $this->settings->get($key);
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
        return array_map(fn($size) => "{$this->faviconBasePath}{$base}_{$size}.png", array_keys($this->getFaviconSizes()));
    }
}
