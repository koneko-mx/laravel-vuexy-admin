<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UI\Avatar;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Typography\FontFactory;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AvatarInitialsService
{
    protected string $avatarDisk;
    protected string $initialAvatarDir;
    protected int $avatarSize;
    protected string $defaultBackground;
    protected array $avatarColors;
    protected float $fontSizeRatio;
    protected string $fallbackText;
    protected string $fontPath;

    public function __construct()
    {
        $this->avatarDisk = env('VUEXY_AVATAR_INITIALS_DISK', 'public');
        $this->initialAvatarDir = env('VUEXY_AVATAR_INITIALS_DIRECTORY', 'initial-avatars');
        $this->avatarSize = (int) env('VUEXY_AVATAR_INITIALS_SIZE', 512);
        $this->defaultBackground = env('VUEXY_AVATAR_INITIALS_BACKGROUND', '#EBF4FF');
        $this->avatarColors = json_decode(env('VUEXY_AVATAR_INITIALS_COLORS', '[]'), true) ?: [
            '#3b82f6', '#2563eb', '#1d4ed8', '#ef4444', '#dc2626', '#b91c1c',
            '#f59e0b', '#d97706', '#b45309', '#10b981', '#059669', '#047857'
        ];
        $this->fontSizeRatio = (float) env('VUEXY_AVATAR_INITIALS_FONT_SIZE_RATIO', 0.4);
        $this->fallbackText = env('VUEXY_AVATAR_INITIALS_FALLBACK_TEXT', 'NA');
        $this->fontPath = dirname(__DIR__, 4) . '/storage/fonts/OpenSans-Bold.ttf';
    }

    public function getAvatarImage(
        string $name,
        ?string $color = null,
        ?string $background = null,
        ?int $size = null,
        ?int $maxLength = null
    ): BinaryFileResponse {
        $avatarConfig = $this->resolveAvatarConfig($name, $color, $background, $size, $maxLength);
        $filePath = "{$this->initialAvatarDir}/{$this->generateCacheKey($avatarConfig)}.png";

        if (!Storage::disk($this->avatarDisk)->exists($filePath)) {
            $image = $this->createAvatarImage($avatarConfig);
            Storage::disk($this->avatarDisk)->put($filePath, $image->toPng(indexed: true));
        }

        return response()->file(Storage::disk($this->avatarDisk)->path($filePath));
    }

    protected function resolveAvatarConfig(
        string $name,
        ?string $color,
        ?string $background,
        ?int $size,
        ?int $maxLength
    ): array {
        return [
            'initials' => $this->getInitials($name, $maxLength),
            'color' => $color ?? $this->getAvatarColor($name),
            'background' => $background ?? $this->defaultBackground,
            'size' => $size ?? $this->avatarSize,
            'font_path' => $this->fontPath,
            'font_size' => ($size ?? $this->avatarSize) * $this->fontSizeRatio
        ];
    }

    protected function generateCacheKey(array $config): string
    {
        return md5(implode('-', [
            $config['initials'],
            $config['color'],
            $config['background'],
            $config['size']
        ]));
    }

    protected function createAvatarImage(array $config): \Intervention\Image\Image
    {
        $manager = new ImageManager(config('image.driver', 'gd'));

        return $manager->create($config['size'], $config['size'])
            ->fill($config['background'])
            ->text($config['initials'], $config['size'] / 2, $config['size'] / 2,
                function (FontFactory $font) use ($config) {
                    $font->file($config['font_path']);
                    $font->size($config['font_size']);
                    $font->color($config['color']);
                    $font->align('center');
                    $font->valign('middle');
                }
            );
    }

    public static function getInitials(string $name, ?int $maxLength = null): string
    {
        $maxLength = $maxLength ?? (int) env('VUEXY_AVATAR_INITIALS_MAX_LENGTH', 2);
        $fallback = env('VUEXY_AVATAR_INITIALS_FALLBACK_TEXT', 'NA');

        if (empty(trim($name))) {
            return $fallback;
        }

        $initials = implode('', array_map(
            fn ($word) => mb_substr(trim($word), 0, 1),
            preg_split('/\s+/', $name)
        ));

        return mb_substr(strtoupper($initials), 0, $maxLength) ?: $fallback;
    }

    public function getAvatarColor(string $name): string
    {
        $hash = array_sum(array_map('ord', str_split($name)));
        return $this->avatarColors[$hash % count($this->avatarColors)];
    }

    public function cleanupOldAvatars(int $olderThanDays = 30, bool $dryRun = false): int
    {
        if ($olderThanDays < 1) {
            throw new \InvalidArgumentException('El número de días debe ser mayor o igual a 1');
        }

        $threshold = now()->subDays($olderThanDays)->timestamp;
        $deleted = 0;
        $disk = Storage::disk($this->avatarDisk);

        // Verificar si el directorio existe primero
        if (!$disk->exists($this->initialAvatarDir)) {
            return 0;
        }

        foreach ($disk->allFiles($this->initialAvatarDir) as $file) {
            try {
                if ($disk->lastModified($file) < $threshold) {
                    if (!$dryRun) {
                        $disk->delete($file);
                    }
                    $deleted++;
                }
            } catch (\Exception $e) {
                // Loggear el error pero continuar con otros archivos
                \Log::error("Error al procesar archivo de avatar: {$file}", ['error' => $e->getMessage()]);
                continue;
            }
        }

        return $deleted;
    }

    public function clearAllInitials(): void
    {
        $disk = Storage::disk($this->avatarDisk);

        try {
            if ($disk->exists($this->initialAvatarDir)) {
                $disk->deleteDirectory($this->initialAvatarDir);
            }
        } catch (\Exception $e) {
            \Log::error("Error al eliminar directorio de avatares", ['error' => $e->getMessage()]);
            throw $e; // Opcional: relanzar si quieres que el llamador maneje el error
        }

        $this->ensureInitialAvatarDirectoryExists();
    }

    protected function ensureInitialAvatarDirectoryExists(): void
    {
        $disk = Storage::disk($this->avatarDisk);

        if (!$disk->exists($this->initialAvatarDir)) {
            try {
                $disk->makeDirectory($this->initialAvatarDir);

                // Opcional: establecer permisos si es necesario
                // chmod($disk->path($this->initialAvatarDir), 0755);
            } catch (\Exception $e) {
                \Log::error("Error al crear directorio de avatares", [
                    'directory' => $this->initialAvatarDir,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        }
    }

    public function getAvatarStorageStats(): array
    {
        $disk = Storage::disk($this->avatarDisk);

        return [
            'total_files' => $disk->exists($this->initialAvatarDir) ? count($disk->allFiles($this->initialAvatarDir)) : 0,
            'total_size' => $disk->size($this->initialAvatarDir),
            'last_modified' => $disk->lastModified($this->initialAvatarDir),
        ];
    }

}
