<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Traits\Logger;

use Illuminate\Database\Eloquent\Model;
use Koneko\VuexyAdmin\Models\SystemLog;
use Koneko\VuexyAdmin\Application\Enums\SystemLog\LogLevel;

trait HasDefaultLoggerMethods
{
    public function logInfo(string $message, array $context = [], ?Model $related = null): SystemLog
    {
        return $this->log(LogLevel::Info, $message, $context, $related);
    }

    public function logWarning(string $message, array $context = [], ?Model $related = null): SystemLog
    {
        return $this->log(LogLevel::Warning, $message, $context, $related);
    }

    public function logError(string $message, array $context = [], ?Model $related = null): SystemLog
    {
        return $this->log(LogLevel::Error, $message, $context, $related);
    }

    abstract protected function log(LogLevel $level, string $message, array $context = [], ?Model $related = null): SystemLog;
}