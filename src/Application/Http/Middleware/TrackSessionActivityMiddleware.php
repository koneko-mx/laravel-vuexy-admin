<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth,Session};
use Koneko\VuexyAdmin\Models\UserLogin;

class TrackSessionActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() && Session::has('user_last_login_id')) {
            $this->closeExpiredSession();
        }

        if (Auth::check()) {
            $this->handleIdleTimeout($request); // antes de actualizar la actividad
            $this->updateLastActivity();
        }

        return $next($request);
    }

    /**
     * Cierra la sesión de usuario marcada como activa cuando detectamos expiración.
     */
    private function closeExpiredSession(): void
    {
        $lastLoginId = Session::pull('user_last_login_id'); // pull: obtiene y elimina al mismo tiempo

        if (!$lastLoginId) {
            return;
        }

        $userLogin = UserLogin::where('id', $lastLoginId)
            ->whereNull('logout_at')
            ->first();

        if ($userLogin) {
            $userLogin->update([
                'logout_at'     => now(),
                'logout_reason' => 'session_expired',
            ]);

            logger()->info("[TrackSessionActivity] ✅ Logout automático registrado para login_id: {$lastLoginId}");
        }
    }

    /**
     * Guarda el timestamp actual como última actividad.
     */
    private function updateLastActivity(): void
    {
        Session::put('last_activity_at', now());
    }

    private function handleIdleTimeout(Request $request): void
    {
        $timeoutMinutes = config('session.idle_timeout', 30); // puedes agregar en tu config/session.php
        $lastActivity = Session::get('last_activity_at');

        if ($lastActivity && now()->diffInMinutes($lastActivity) >= $timeoutMinutes) {
            $this->forceLogoutDueToIdle();
        }
    }

    private function forceLogoutDueToIdle(): void
    {
        $lastLoginId = Session::pull('user_last_login_id');

        if ($lastLoginId) {
            $userLogin = UserLogin::where('id', $lastLoginId)
                ->whereNull('logout_at')
                ->first();

            if ($userLogin) {
                $userLogin->update([
                    'logout_at'     => now(),
                    'logout_reason' => 'idle_timeout',
                ]);

                logger()->info("[TrackSessionActivity] ⏳ Sesión cerrada por inactividad para login_id: {$lastLoginId}");
            }
        }

        Auth::logout(); // Use Auth facade to logout
        Session::invalidate(); // limpia toda la sesión
    }
}
