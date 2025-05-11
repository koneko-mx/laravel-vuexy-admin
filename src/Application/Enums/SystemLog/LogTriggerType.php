<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Enums\SystemLog;

enum LogTriggerType: string
{
    case User     = 'user';
    case Cron     = 'cronjob';
    case Seeder   = 'seeder';
    case Listener = 'listener';
    case Webhook  = 'webhook';
    case System   = 'system';
    case Api      = 'api';
    case Job      = 'job';
}
