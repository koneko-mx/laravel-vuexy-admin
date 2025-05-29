<?php

namespace Koneko\VuexyAdmin\Application\Contracts\Loggers;

use Illuminate\Database\Eloquent\Model;
use Koneko\VuexyAdmin\Application\Enums\SystemLog\LogTriggerType;
use Koneko\VuexyAdmin\Application\Enums\SystemLog\LogLevel;
use Koneko\VuexyAdmin\Models\SystemLog;

interface SystemLoggerInterface
{
    public function log(
        string|LogLevel $level,
        string $message,
        array $context = [],
        ?Model $relatedModel = null,
        LogTriggerType $triggerType = LogTriggerType::System,
        ?int $triggerId = null
    ): SystemLog;
}
