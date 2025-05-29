<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UX\Notifications\Builder;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class VuexyNotificationsBuilder
{
    /** @var string Settings Context */
    private const GROUP     = 'notifications';
    private const SECTION   = 'website-admin';
    private const SUB_GROUP = 'persistent';

    /** @var Model Scope */
    private const SCOPE = User::class;

    /** @var string Cache keyName */
    private const KEY_NAME = 'notifications';

    /** @var Authenticatable User */
    private ?Authenticatable $user = null;

    public function __construct(?Authenticatable $user = null)
    {
        $this->user = $user ?? Auth::user();
        //$this->initCacheConfig();
    }

    public function getNotifications(): array
    {
        return [];
    }
}
