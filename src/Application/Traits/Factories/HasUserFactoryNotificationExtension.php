<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\Factories;

use Koneko\VuexyAdmin\Models\Notification;

trait HasUserFactoryNotificationExtension
{
    /**
     * Agrega notificaciones fake al usuario tras crearlo.
     *
     * @param int $min Cantidad mínima de notificaciones
     * @param int $max Cantidad máxima de notificaciones
     * @return self
     */
    public function withNotifications(int $min = 1, int $max = 5): self
    {
        return $this->afterCreating(function ($user) use ($min, $max) {
            $count = rand($min, $max);

            if ($count > 0) {
                Notification::factory()
                    ->count($count)
                    ->state([
                        'user_id' => $user->id,
                    ])
                    ->create();
            }
        });
    }
}
