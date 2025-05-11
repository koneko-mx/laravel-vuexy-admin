<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Koneko\VuexyAdmin\Models\SystemLog;
use Koneko\VuexyAdmin\Application\Enums\{LogLevel, LogTriggerType};

class ____SystemLoggerService
{
    /**
     * Registra un log en la tabla system_logs.
     */
    public function log(
        string|LogLevel $level,
        string $module,
        string $message,
        array $context = [],
        LogTriggerType $triggerType = LogTriggerType::System,
        ?int $triggerId = null,
        ?Model $relatedModel = null
    ): SystemLog {
        return SystemLog::create([
            'module'       => $module,
            'level'        => $level instanceof LogLevel ? $level : LogLevel::from($level),
            'message'      => $message,
            'context'      => $context,
            'trigger_type' => $triggerType,
            'trigger_id'   => $triggerId,
            'user_id'      => Auth::id(),
            'related_model_type' => $relatedModel?->getMorphClass(),
            'related_model_id'   => $relatedModel?->getKey(),
        ]);
    }

    public function info(
        string $module,
        string $message,
        array $context = [],
        LogTriggerType $triggerType = LogTriggerType::System,
        ?int $triggerId = null,
        ?Model $relatedModel = null
    ): SystemLog {
        return $this->log(LogLevel::Info, $module, $message, $context, $triggerType, $triggerId, $relatedModel);
    }

    public function warning(
        string $module,
        string $message,
        array $context = [],
        LogTriggerType $triggerType = LogTriggerType::System,
        ?int $triggerId = null,
        ?Model $relatedModel = null
    ): SystemLog {
        return $this->log(LogLevel::Warning, $module, $message, $context, $triggerType, $triggerId, $relatedModel);
    }

    public function error(
        string $module,
        string $message,
        array $context = [],
        LogTriggerType $triggerType = LogTriggerType::System,
        ?int $triggerId = null,
        ?Model $relatedModel = null
    ): SystemLog {
        return $this->log(LogLevel::Error, $module, $message, $context, $triggerType, $triggerId, $relatedModel);
    }

    public function debug(
        string $module,
        string $message,
        array $context = [],
        LogTriggerType $triggerType = LogTriggerType::System,
        ?int $triggerId = null,
        ?Model $relatedModel = null
    ): SystemLog {
        return $this->log(LogLevel::Debug, $module, $message, $context, $triggerType, $triggerId, $relatedModel);
    }
}
