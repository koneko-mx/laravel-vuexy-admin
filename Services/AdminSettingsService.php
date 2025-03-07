<?php

namespace Koneko\VuexyAdmin\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Koneko\VuexyAdmin\Models\Setting;

class AdminSettingsService
{
    private $driver;
    private $imageDisk           = 'public';
    private $favicon_basePath    = 'favicon/';
    private $image_logo_basePath = 'images/logo/';

    private $faviconsSizes = [
        '180x180' => [180, 180],
        '192x192' => [192, 192],
        '152x152' => [152, 152],
        '120x120' => [120, 120],
        '76x76' => [76, 76],
        '16x16' => [16, 16],
    ];

    private $imageLogoMaxPixels1 =  22500; // Primera versión (px^2)
    private $imageLogoMaxPixels2 =  75625; // Segunda versión (px^2)
    private $imageLogoMaxPixels3 = 262144; // Tercera versión (px^2)
    private $imageLogoMaxPixels4 = 230400; // Tercera versión (px^2) en Base64

    protected $cacheTTL = 60 * 24 * 30; // 30 días en minutos

    public function __construct()
    {
        $this->driver = config('image.driver', 'gd');
    }

    public function updateSetting(string $key, string $value): bool
    {
        $setting = Setting::updateOrCreate(
            ['key' => $key],
            ['value' => trim($value)]
        );

        return $setting->save();
    }

    public function processAndSaveFavicon($image): void
    {
        Storage::makeDirectory($this->imageDisk . '/' . $this->favicon_basePath);

        // Eliminar favicons antiguos
        $this->deleteOldFavicons();

        // Guardar imagen original
        $imageManager = new ImageManager($this->driver);

        $imageName = uniqid('admin_favicon_');

        $image = $imageManager->read($image->getRealPath());

        foreach ($this->faviconsSizes as $size => [$width, $height]) {
            $resizedPath = $this->favicon_basePath . $imageName . "_{$size}.png";

            $image->cover($width, $height);

            Storage::disk($this->imageDisk)->put($resizedPath, $image->toPng(indexed: true));
        }

        $this->updateSetting('admin_favicon_ns', $this->favicon_basePath . $imageName);
    }

    protected function deleteOldFavicons(): void
    {
        // Obtener el favicon actual desde la base de datos
        $currentFavicon = Setting::where('key', 'admin_favicon_ns')->value('value');

        if ($currentFavicon) {
            $filePaths = [
                $this->imageDisk . '/' . $currentFavicon,
                $this->imageDisk . '/' . $currentFavicon . '_16x16.png',
                $this->imageDisk . '/' . $currentFavicon . '_76x76.png',
                $this->imageDisk . '/' . $currentFavicon . '_120x120.png',
                $this->imageDisk . '/' . $currentFavicon . '_152x152.png',
                $this->imageDisk . '/' . $currentFavicon . '_180x180.png',
                $this->imageDisk . '/' . $currentFavicon . '_192x192.png',
            ];

            foreach ($filePaths as $filePath) {
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                }
            }
        }
    }

    public function processAndSaveImageLogo($image, string $type = ''): void
    {
        // Crear directorio si no existe
        Storage::makeDirectory($this->imageDisk . '/' . $this->image_logo_basePath);

        // Eliminar imágenes antiguas
        $this->deleteOldImageWebapp($type);

        // Leer imagen original
        $imageManager = new ImageManager($this->driver);
        $image = $imageManager->read($image->getRealPath());

        // Generar tres versiones con diferentes áreas máximas
        $this->generateAndSaveImage($image, $type, $this->imageLogoMaxPixels1, 'small'); // Versión 1
        $this->generateAndSaveImage($image, $type, $this->imageLogoMaxPixels2, 'medium'); // Versión 2
        $this->generateAndSaveImage($image, $type, $this->imageLogoMaxPixels3); // Versión 3
        $this->generateAndSaveImageAsBase64($image, $type, $this->imageLogoMaxPixels4); // Versión 3
    }

    private function generateAndSaveImage($image, string $type, int $maxPixels, string $suffix = ''): void
    {
        $imageClone = clone $image;

        // Escalar imagen conservando aspecto
        $this->resizeImageToMaxPixels($imageClone, $maxPixels);

        $imageName = 'admin_image_logo' . ($suffix ? '_' . $suffix : '') . ($type == 'dark' ? '_dark' : '');

        // Generar nombre y ruta
        $imageNameUid = uniqid($imageName .  '_',  ".png");
        $resizedPath = $this->image_logo_basePath . $imageNameUid;

        // Guardar imagen en PNG
        Storage::disk($this->imageDisk)->put($resizedPath, $imageClone->toPng(indexed: true));

        // Actualizar configuración
        $this->updateSetting($imageName, $resizedPath);
    }

    private function resizeImageToMaxPixels($image, int $maxPixels)
    {
        // Obtener dimensiones originales de la imagen
        $originalWidth = $image->width();  // Método para obtener el ancho
        $originalHeight = $image->height(); // Método para obtener el alto

        // Calcular el aspecto
        $aspectRatio = $originalWidth / $originalHeight;

        // Calcular dimensiones redimensionadas conservando aspecto
        if ($aspectRatio > 1) { // Ancho es dominante
            $newWidth = sqrt($maxPixels * $aspectRatio);
            $newHeight = $newWidth / $aspectRatio;
        } else { // Alto es dominante
            $newHeight = sqrt($maxPixels / $aspectRatio);
            $newWidth = $newHeight * $aspectRatio;
        }

        // Redimensionar la imagen
        $image->resize(
            round($newWidth), // Redondear para evitar problemas con números decimales
            round($newHeight),
            function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            }
        );

        return $image;
    }


    private function generateAndSaveImageAsBase64($image, string $type, int $maxPixels): void
    {
        $imageClone = clone $image;

        // Redimensionar imagen conservando el aspecto
        $this->resizeImageToMaxPixels($imageClone, $maxPixels);

        // Convertir a Base64
        $base64Image = (string) $imageClone->toJpg(40)->toDataUri();

        // Guardar como configuración
        $this->updateSetting(
            "admin_image_logo_base64" . ($type === 'dark' ? '_dark' : ''),
            $base64Image // Ya incluye "data:image/png;base64,"
        );
    }

    protected function deleteOldImageWebapp(string $type = ''): void
    {
        // Determinar prefijo según el tipo (normal o dark)
        $suffix = $type === 'dark' ? '_dark' : '';

        // Claves relacionadas con las imágenes que queremos limpiar
        $imageKeys = [
            "admin_image_logo{$suffix}",
            "admin_image_logo_small{$suffix}",
            "admin_image_logo_medium{$suffix}",
        ];

        // Recuperar las imágenes actuales en una sola consulta
        $settings = Setting::whereIn('key', $imageKeys)->pluck('value', 'key');

        foreach ($imageKeys as $key) {
            // Obtener la imagen correspondiente
            $currentImage = $settings[$key] ?? null;

            if ($currentImage) {
                // Construir la ruta del archivo y eliminarlo si existe
                $filePath = $this->imageDisk . '/' . $currentImage;
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                }

                // Eliminar la configuración de la base de datos
                Setting::where('key', $key)->delete();
            }
        }
    }
}
