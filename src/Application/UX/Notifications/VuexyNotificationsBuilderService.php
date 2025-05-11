<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UX\Notifications;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Koneko\VuexyAdmin\Support\Traits\Cache\InteractsWithKonekoVarsCache;

class VuexyNotificationsBuilderService
{
    use InteractsWithKonekoVarsCache;

    private ?Authenticatable $user = null;

    private const CACHE_PREFIX = 'vuexy_quick_links_user_id:';

    public function __construct(?Authenticatable $user = null)
    {
        $this->user = $user ?? Auth::user();
        $this->initCacheConfig();
    }

    public function getForUser(): array
    {
        return [];
    }
}