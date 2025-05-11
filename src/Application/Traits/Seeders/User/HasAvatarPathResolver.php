<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\Seeders\User;

trait HasAvatarPathResolver
{
    protected function resolveAvatarPath(string $avatarPath): string|false
    {
        $paths = [
            base_path($avatarPath),
            storage_path($avatarPath),
            public_path($avatarPath),
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) return $path;
        }

        return false;
    }

    protected function resolveAvatarFilePaths(array $relativePaths): array
    {
        $roots = [base_path(), storage_path(), public_path()];

        return collect($relativePaths)
            ->flatMap(fn($relPath) => collect($roots)
                ->map(fn($root) => rtrim("{$root}/" . ltrim($relPath, '/'), '/'))
                ->filter(fn($fullPath) => is_dir($fullPath))
                ->flatMap(fn($validDir) => collect($this->getImagesRecursively($validDir)))
            )
            ->filter(fn($file) => is_file($file))
            ->unique()
            ->values()
            ->all();
    }

    protected function getImagesRecursively(string $directory): array
    {
        $extensions = ['jpg','jpeg','png','webp','bmp','svg','gif','tiff','tif'];

        return collect($extensions)
            ->flatMap(fn($ext) => glob("{$directory}/**/*.{$ext}", \GLOB_NOSORT) ?: [])
            ->unique()
            ->values()
            ->all();
    }
}
