<?php

namespace Koneko\VuexyAdmin\Alication\Logger;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Koneko\VuexyAdmin\Application\Enums\SystemLog\{LogTriggerType, LogLevel};
use Koneko\VuexyAdmin\Application\Bootstrap\KonekoComponentContextRegistrar;
use Koneko\VuexyAdmin\Models\SystemLog;

/**
 * ✨ Logger de sistema contextual para el ecosistema Koneko
 */
class KonekoSystemLogger
{
    protected string $component;

    public function __construct(?string $component = null)
    {
        $this->component = $component ?? KonekoComponentContextRegistrar::currentComponent() ?? 'core';
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
            'user_id'      => auth()->id(),
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
