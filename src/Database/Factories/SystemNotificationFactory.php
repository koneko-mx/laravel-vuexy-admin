<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Database\Factories;

use Illuminate\Support\{Carbon, Str};
use Koneko\VuexyAdmin\Support\Enums\SystemNotifications\{
    SystemNotificationScope,
    SystemNotificationType,
    SystemNotificationStyle,
    SystemNotificationPriority
};
use Koneko\VuexyAdmin\Models\{SystemNotification, SystemNotificationUser};
use Koneko\VuexyAdmin\Support\Factories\Base\AbstractModelFactory;
use Koneko\VuexyAdmin\Models\User;

class SystemNotificationFactory extends AbstractModelFactory
{
    protected $model = SystemNotification::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(SystemNotificationType::cases());
        $scope = $this->faker->randomElement(SystemNotificationScope::cases());
        $style = $this->faker->randomElement(SystemNotificationStyle::cases());
        $priority = $this->faker->randomElement(SystemNotificationPriority::cases());

        return [
            'scope'                  => $scope,
            'type'                   => $type,
            'style'                  => $style,
            'priority'               => $priority,
            'requires_confirmation'  => $this->faker->boolean(30),
            'title'                  => Str::headline("Aviso de {$type->value}"),
            'message'                => $this->faker->realTextBetween(80, 180),
            'target_area'            => $this->faker->randomElement(['header', 'footer', 'dashboard', 'popup']),
            'tags'                   => [$this->faker->slug(), $this->faker->slug()],
            'roles'                  => $this->faker->randomElement([null, ['admin'], ['cliente'], ['staff']]),
            'user_flags'             => $this->faker->randomElement([null, ['is_customer'], ['is_premium'], ['is_partner']]),
            'is_active'              => true,
            'starts_at'              => Carbon::now()->subDays(rand(0, 2)),
            'ends_at'                => Carbon::now()->addDays(rand(2, 10)),
            'created_by'             => null,
            'updated_by'             => null,
        ];
    }

    /**
     * Asocia uno o varios usuarios con lectura y confirmación simulada
     */
    public function withUsers(int $total = 3, bool $simulateRead = true, bool $simulateConfirm = false): self
    {
        return $this->afterCreating(function (SystemNotification $notification) use ($total, $simulateRead, $simulateConfirm) {
            $users = User::inRandomOrder()->take($total)->get();

            foreach ($users as $user) {
                $status = new SystemNotificationUser([
                    'is_read'      => $simulateRead,
                    'read_at'      => $simulateRead ? now()->subMinutes(rand(1, 60)) : null,
                    'is_dismissed' => $this->faker->boolean(50),
                    'dismissed_at' => $this->faker->boolean(50) ? now()->subMinutes(rand(1, 60)) : null,
                    'is_confirmed' => $simulateConfirm,
                    'confirmed_at' => $simulateConfirm ? now() : null,
                    'confirmation_notes' => $simulateConfirm ? 'Confirmación automática (faker).' : null,
                ]);

                $notification->userStatuses()->save($status->user()->associate($user));
            }
        });
    }
}
