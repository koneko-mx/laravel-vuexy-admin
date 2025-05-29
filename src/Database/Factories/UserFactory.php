<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Database\Factories;

use Koneko\VuexyAdmin\Application\Enums\User\UserBaseFlags;
use Koneko\VuexyAdmin\Application\Traits\Factories\{HasUserFactoryRoleExtension,HasUserFactoryAvatarExtension, HasUserFactoryNotificationExtension};
use Koneko\VuexyAdmin\Models\User;
use Koneko\VuexyAdmin\Support\Factories\Base\AbstractModelFactory;
use Koneko\VuexyAdmin\Support\Traits\Flags\Factories\HasUserFactoryFlagsExtension;
use Koneko\VuexyAdmin\Support\Traits\Factories\HasDynamicFactoryExtenders;

/**
 * 🧲 UserFactory
 *
 * Factory oficial del modelo `User` dentro del ecosistema Koneko Vuexy ERP.
 *
 * - Relaciones y claves foráneas simuladas
 *
 * @extends AbstractModelFactory<User>
 */
class UserFactory extends AbstractModelFactory
{
    use HasDynamicFactoryExtenders;
    use HasUserFactoryRoleExtension,
        HasUserFactoryAvatarExtension,
        HasUserFactoryNotificationExtension;
    use HasUserFactoryFlagsExtension;

    protected $model = User::class;

    public ?string $upload_profile_photo = null;

    protected function baseDefinition(): array
    {
        return [
            'name'       => $this->faker->firstName,
            'last_name'  => $this->maybe(80, $this->faker->lastName),
            'email'      => $this->generateNiceEmail(),
            'email_verified_at' => $this->maybeDefault(now()),
            'password'   => bcrypt('password'),
            'flags'      => [UserBaseFlags::IS_USER->value => 1],
            'status'     => $this->faker->boolean(90),
            'created_by' => $this->maybeDefault(User::inRandomOrder()->value('id')),
        ];
    }

    public function definition(): array
    {
        return array_merge(
            $this->baseDefinition(),
            $this->collectExtensionDefinitions(),
        );
    }

}
