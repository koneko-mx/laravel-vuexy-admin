<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Database\Factories;

use Carbon\Carbon;
use Koneko\VuexyAdmin\Models\Notification;
use Koneko\VuexyAdmin\Models\User;
use Koneko\VuexyAdmin\Support\Factories\Base\AbstractModelFactory;
use Koneko\VuexyAdmin\Support\Traits\Factories\HasFactorySupport;

/**
 * 🧲 NotificationFactory
 *
 * Generador oficial de notificaciones del sistema para pruebas o seeders en el ERP.
 */
class NotificationFactory extends AbstractModelFactory
{
    use HasFactorySupport;

    protected $model = Notification::class;

    public function definition(): array
    {
        return $this->baseDefinition();
    }

    protected function baseDefinition(): array
    {
        $type             = $this->randomFrom(['info', 'success', 'warning', 'danger', 'system']);
        $isRead           = $this->faker->boolean(40);
        $shouldHaveData   = $this->faker->boolean(60);
        $shouldHaveAction = $this->faker->boolean(35);
        $shouldBeDeleted  = $this->faker->boolean(10);

        return [
            'type'          => $type,
            'module'        => $this->randomFrom(['core', 'audit', 'system']),
            'title'         => $this->generateTitle($type),
            'body'          => $this->faker->paragraph,
            'data'          => $shouldHaveData ? ['context' => $this->faker->sentence(3)] : null,
            'action_url'    => $shouldHaveAction ? $this->generateActionUrl($type) : null,
            'is_read'       => $isRead,
            'read_at'       => $isRead ? Carbon::now()->subMinutes(rand(1, 1440)) : null,
            'is_dismissed'  => $this->faker->boolean(15),
            'is_deleted'    => $isRead ? $shouldBeDeleted : false,
            'user_id'       => $this->getRandomUserId(),
            'emitted_by'    => $this->maybe(60, fn() => $this->getRandomUserId()),
        ];
    }

    protected function generateTitle(string $type): string
    {
        return match ($type) {
            'info'    => 'ℹ️ Información general',
            'success' => '✅ Operación completada',
            'warning' => '⚠️ Acción requerida',
            'danger'  => '❌ Error detectado',
            'system'  => '🛠️ Mensaje del sistema',
            default   => '🔔 Notificación',
        };
    }

    protected function generateActionUrl(string $type): string
    {
        return match ($type) {
            'info', 'success' => route('admin.core.pages.home.index'),
            'warning'         => route('admin.core.pages.about.index'),
            'danger'          => route('admin.core.audit.users-auth-logs.index'),
            'system'          => route('admin.core.audit.security-events.index'),
            default           => route('admin.core.pages.home.index'),
        };
    }

    protected function getRandomUserId(): ?int
    {
        return User::inRandomOrder()->value('id');
    }

    // 📦 States opcionales
    public function read(): self
    {
        return $this->state(fn() => [
            'is_read'  => true,
            'read_at'  => Carbon::now(),
        ]);
    }

    public function dismissed(): self
    {
        return $this->state(fn() => ['is_dismissed' => true]);
    }

    public function forUser(int $userId): self
    {
        return $this->state(fn() => ['user_id' => $userId]);
    }
}
