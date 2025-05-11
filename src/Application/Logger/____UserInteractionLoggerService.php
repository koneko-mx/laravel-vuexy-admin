<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Logger;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Koneko\VuexyAdmin\Models\UserInteraction;
use Koneko\VuexyAdmin\Application\Enums\UserInteractions\InteractionSecurityLevel;
use Koneko\VuexyAdmin\Application\Bootstrap\KonekoModuleRegistry;

class ____UserInteractionLoggerService
{
    public function record(
        string $action,
        array $context = [],
        InteractionSecurityLevel|string $security = InteractionSecurityLevel::Normal,
        ?string $livewireComponent = null
    ): ?UserInteraction {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        $component = $this->resolveCurrentComponent();

        return UserInteraction::create([
            'user_id'            => $user->id,
            'actor_type'         => $user->actor_type ?? 'unknown',
            'component'          => $component,
            'livewire_component' => $livewireComponent,
            'action'             => $action,
            'security_level'     => is_string($security) ? $security : $security->value,
            'ip_address'         => Request::ip(),
            'user_agent'         => Request::userAgent(),
            'context'            => $context,
            'user_flags'         => $user->flags ?? [],
            'user_roles'         => $user->roles ?? [],
        ]);
    }

    /**
     * Infiera el componente actual (el módulo) para la auditoría.
     */
    private function resolveCurrentComponent(): string
    {
        // Opcional: Puedes mejorarlo si quieres tracking de "módulo activo"
        $modules = KonekoModuleRegistry::enabled();

        if (empty($modules)) {
            return 'unknown-module';
        }

        // Si hay muchos módulos activos, podrías basarlo en la ruta actual
        $currentRoute = request()->route()?->getName();

        foreach ($modules as $module) {
            if (str_contains($currentRoute, $module->slug)) {
                return $module->getId(); // Este es slugificado
            }
        }

        // Fallback: solo devuelve el primero activo
        return reset($modules)?->getId() ?? 'unknown-module';
    }
}
