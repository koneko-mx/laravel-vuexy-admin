<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Loggers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Koneko\VuexyAdmin\Application\Bootstrap\Registry\KonekoModuleRegistry;
use Koneko\VuexyAdmin\Models\UserInteraction;
use Koneko\VuexyAdmin\Models\User;
use Koneko\VuexyAdmin\Support\Enums\UserInteractions\InteractionSecurityLevel;

/**
 * 📋 Logger de interacciones de usuario con nivel de seguridad.
 */
class KonekoUserInteractionLogger
{
    public static function record(
        string $action,
        array $context = [],
        InteractionSecurityLevel|string $security = 'normal',
        ?string $livewireComponent = null,
        ?int $userId = null
    ): ?UserInteraction {
        $userId = $userId ?? Auth::id();
        if (!$userId) {
            return null;
        }

        /** @var User $user */
        $user = Auth::user();

        return UserInteraction::create([
            'module'            => KonekoModuleRegistry::current()->componentNamespace ?? 'core',
            'user_id'           => $userId,
            'action'            => $action,
            'livewire_component'=> $livewireComponent,
            'security_level'    => is_string($security) ? InteractionSecurityLevel::from($security) : $security,
            'ip_address'        => Request::ip(),
            'user_agent'        => Request::userAgent(),
            'context'           => $context,
            'user_flags'        => $user->flags_array ?? [],
            'user_roles'        => $user->getRoleNames()->toArray(),
        ]);
    }
}
