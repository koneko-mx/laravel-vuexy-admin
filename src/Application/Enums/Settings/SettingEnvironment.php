<?php

namespace Koneko\VuexyAdmin\Application\Enums\Settings;

use Koneko\VuexyAdmin\Support\Traits\Enums\HasEnumHelpers;

enum SettingEnvironment: string
{
    use HasEnumHelpers;

    case PROD    = 'prod';
    case DEV     = 'dev';
    case STAGING = 'staging';
    case TEST    = 'test';
}
