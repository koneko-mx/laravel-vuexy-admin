<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\Seeders\User;

use Koneko\VuexyAdmin\Application\UI\Avatar\AvatarImageService;
use Koneko\VuexyAdmin\Models\User;

trait HandlesSeederAvatars
{
    use HasAvatarPathResolver;

    protected function assignAvatarToUser(string $email, string $avatarPath): void
    {
        $user = User::where('email', $email)->first();
        $avatarPath = $this->resolveAvatarPath($avatarPath);

        if (!$user || !$avatarPath) return;

        try {
            app(AvatarImageService::class)->updateProfilePhoto($user, $avatarPath);

        } catch (\Throwable $e) {
            $this->log("⚠️ Error asignando avatar a {$email}: " . $e->getMessage());
        }
    }
}
