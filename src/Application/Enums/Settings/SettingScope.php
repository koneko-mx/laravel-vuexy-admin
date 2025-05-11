<?php

namespace Koneko\VuexyAdmin\Application\Enums\Settings;

use Koneko\VuexyAdmin\Support\Traits\Enums\HasEnumHelpers;

enum SettingScope: string
{
    use HasEnumHelpers;

    case GLOBAL = 'global';
    case TENANT = 'tenant';
    case BRANCH = 'branch';
    case USER   = 'user';
    case GUEST  = 'guest';
}
