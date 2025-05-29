<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Enums\SystemLog;

enum LogLevel: string
{
    case Info    = 'info';
    case Warning = 'warning';
    case Error   = 'error';
    case Debug   = 'debug';
}
