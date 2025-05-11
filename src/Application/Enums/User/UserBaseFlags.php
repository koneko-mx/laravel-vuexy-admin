<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Enums\User;

use Koneko\VuexyAdmin\Models\User;
use Koneko\VuexyAdmin\Support\Traits\Enums\FlagEnumTrait;

enum UserBaseFlags: string
{
    use FlagEnumTrait;

    case IS_USER = 'is_user';

    public static function modelClass(): string
    {
        return User::class;
    }

    public static function getDescription(self $case): string
    {
        return match($case) {
            self::IS_USER => 'Usuario de sistema',
        };
    }

    public function describeActiveFlags(): array
    {
        return collect(static::getRegisteredFlags())
            ->filter(fn ($desc, $flag) => $this->hasFlag($flag))
            ->mapWithKeys(fn ($desc, $flag) => [$flag => $desc])
            ->all();
    }
}
