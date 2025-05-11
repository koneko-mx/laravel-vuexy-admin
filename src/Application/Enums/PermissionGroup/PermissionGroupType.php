<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Enums\PermissionGroup;

enum PermissionGroupType: string
{
    case Module        = 'module';
    case RootGroup     = 'root_group';
    case SubGroup      = 'sub_group';
    case ExternalGroup = 'external_group';
}
