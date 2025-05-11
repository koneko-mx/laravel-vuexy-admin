<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\UX;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Koneko\VuexyAdmin\Models\User;
use Koneko\VuexyAdmin\Support\Helpers\VuexyNotification;

class VuexyNotificationDispatcher
{
    /**
     * Obtiene notificaciones visibles, no leídas ni descartadas, cacheadas por usuario.
     */
    public function getCachedForUser(int|User $user): Collection
    {
        $userId = $user instanceof User ? $user->id : $user;

        return Cache::remember("vuexy_notifications_user_{$userId}", now()->addMinutes(10), function () use ($userId) {
            return VuexyNotification::query()
                ->where('user_id', $userId)
                ->where('is_read', false)
                ->where('is_dismissed', false)
                ->where('is_deleted', false)
                ->latest('created_at')
                ->get();
        });
    }

    /**
     * Elimina la caché de notificaciones de un usuario.
     */
    public function clearCacheForUser(int|User $user): void
    {
        $userId = $user instanceof User ? $user->id : $user;
        Cache::forget("vuexy_notifications_user_{$userId}");
    }

    /**
     * Elimina la caché de todos los usuarios (para mantenimiento).
     */
    public function clearAllCaches(): void
    {
        foreach (User::pluck('id') as $id) {
            Cache::forget("vuexy_notifications_user_{$id}");
        }
    }

    /**
     * Crea una nueva notificación y actualiza la caché.
     */
    public function createNotification(array $data): VuexyNotification
    {
        $notification = VuexyNotification::create($data);
        $this->clearCacheForUser($notification->user_id);
        return $notification;
    }

    /**
     * Marca una notificación como leída.
     */
    public function markAsRead(VuexyNotification $notification): void
    {
        $notification->update(['is_read' => true]);
        $this->clearCacheForUser($notification->user_id);
    }

    /**
     * Descarta una notificación visualmente sin eliminarla.
     */
    public function dismissNotification(VuexyNotification $notification): void
    {
        $notification->update(['is_dismissed' => true]);
        $this->clearCacheForUser($notification->user_id);
    }

    /**
     * Elimina lógicamente una notificación.
     */
    public function deleteNotification(VuexyNotification $notification): void
    {
        $notification->update(['is_deleted' => true]);
        $this->clearCacheForUser($notification->user_id);
    }
}
