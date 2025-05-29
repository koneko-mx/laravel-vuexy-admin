<?php

namespace Koneko\VuexyAdmin\Application\Loggers;

use Illuminate\Database\Eloquent\Model;
use Koneko\VuexyAdmin\Application\Bootstrap\Registry\KonekoModuleRegistry;
use Koneko\VuexyAdmin\Models\SystemLog;
use Koneko\VuexyAdmin\Support\Enums\SystemLog\LogLevel;
use Koneko\VuexyAdmin\Support\Enums\SystemLog\LogTriggerType;
use Illuminate\Support\Facades\Auth;

/**
 * ✨ Logger de sistema contextual para el ecosistema Koneko
 */
class KonekoSystemLogger
{
    protected string $component;

    public function __construct(?string $component = null)
    {
        $this->component = $component ?? KonekoModuleRegistry::current()->componentNamespace ?? 'core';
    }

    public function log(
        LogLevel|string $level,
        string $message,
        array $context = [],
        LogTriggerType|string $triggerType = LogTriggerType::System,
        ?int $triggerId = null,
        ?Model $relatedModel = null
    ): SystemLog {
        return SystemLog::create([
            'module'       => $this->component,
            'user_id'      => Auth::id(),
            'level'        => $level instanceof LogLevel ? $level->value : $level,
            'message'      => $message,
            'context'      => $context,
            'trigger_type' => $triggerType instanceof LogTriggerType ? $triggerType->value : $triggerType,
            'trigger_id'   => $triggerId,
            'loggable_id'  => $relatedModel?->getKey(),
            'loggable_type'=> $relatedModel?->getMorphClass(),
        ]);
    }

    public function info(string $message, array $context = []): SystemLog
    {
        return $this->log(LogLevel::Info, $message, $context);
    }

    public function warning(string $message, array $context = []): SystemLog
    {
        return $this->log(LogLevel::Warning, $message, $context);
    }

    public function error(string $message, array $context = []): SystemLog
    {
        return $this->log(LogLevel::Error, $message, $context);
    }

    public function debug(string $message, array $context = []): SystemLog
    {
        return $this->log(LogLevel::Debug, $message, $context);
    }

    public function withTrigger(LogTriggerType|string $type, ?int $id = null): static
    {
        $clone = clone $this;
        $clone->triggerType = $type;
        $clone->triggerId = $id;
        return $clone;
    }
}
