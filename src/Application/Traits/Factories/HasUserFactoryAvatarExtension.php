<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\Factories;

use Koneko\VuexyAdmin\Application\UI\Avatar\AvatarImageService;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;

trait HasUserFactoryAvatarExtension
{
    public function withAvatar(int $percentage = 20): self
    {
        return $this->afterCreating(function ($user) use ($percentage) {
            $config = config('seeder.modules.user_fake.fake.images');

            if (isset($config['assign_percent'])) {
                $percentage = $config['assign_percent'];
            }

            if (rand(1, 100) > $percentage || empty($config['source'])) {
                return;
            }

            $files = $this->getValidImageFiles($config['source']);

            if ($files->isEmpty()) {
                return;
            }

            $avatarPath = $files->random();

            app(AvatarImageService::class)->updateProfilePhoto($user, $avatarPath);
        });
    }

    protected function getValidImageFiles(array $paths): \Illuminate\Support\Collection
    {
        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'bmp', 'tiff', 'tif'];

        return collect($paths)
            ->flatMap(function ($path) use ($extensions) {
                $fullPath = base_path($path);

                if (!File::exists($fullPath)) {
                    return [];
                }

                // Usar Symfony Finder para búsqueda recursiva
                $finder = new Finder();
                $finder->files()
                    ->in($fullPath)
                    ->name('/\.('.implode('|', $extensions).')$/i');

                $files = [];
                foreach ($finder as $file) {
                    $files[] = $file->getRealPath();
                }

                return $files;
            })
            ->values();
    }
}
