<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Listeners\Authentication;

use Illuminate\Auth\Events\Failed;
use Koneko\VuexyAdmin\Application\Loggers\KonekoSecurityAuditLogger;

class HandleFailedLogin
{
    protected $auditService;

    public function __construct(KonekoSecurityAuditLogger $auditService)
    {
        $this->auditService = $auditService;
    }

    public function handle(Failed $event)
    {
        $request = request();

        $this->auditService->logEvent('failed_login_attempt', $request, $event->user?->id, [
            'credentials' => $event->credentials,
        ]);
    }
}
