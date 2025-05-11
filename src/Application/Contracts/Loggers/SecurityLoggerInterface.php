<?php

namespace Koneko\VuexyAdmin\Application\Contracts\Loggers;

use Illuminate\Http\Request;

interface SecurityLoggerInterface
{
    public function logEvent(
        string $type,
        ?Request $request = null,
        ?int $userId = null,
        array $payload = [],
        bool $isProxy = false
    ): void;
}