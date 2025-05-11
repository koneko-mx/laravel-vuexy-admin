<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Contracts\Enums;

interface LabeledEnumInterface
{
    public function label(): string;

    public static function options(): array;
    public static function labels(): array;
    public static function values(): array;
    public static function validationRules(bool $required = false): array;

}
