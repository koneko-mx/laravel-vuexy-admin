<?php

namespace Koneko\VuexyAdmin\Application\Enums\Settings;

use Koneko\VuexyAdmin\Support\Traits\Enums\HasEnumHelpers;

enum SettingValueType: string
{
    use HasEnumHelpers;

    case STRING  = 'string';
    case INTEGER = 'integer';
    case BOOLEAN = 'boolean';
    case FLOAT   = 'float';
    case TEXT    = 'text';
    case BINARY  = 'binary';
}