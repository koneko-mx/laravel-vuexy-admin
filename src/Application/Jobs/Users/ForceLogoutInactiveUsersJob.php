<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Jobs\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Koneko\VuexyAdmin\Models\UserLogin;

class ForceLogoutInactiveUsersJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(): void
    {
        $timeoutMinutes = config('session.idle_timeout', 30);

        $cutoff = now()->subMinutes($timeoutMinutes);

        $sessions = UserLogin::whereNull('logout_at')
            ->where('created_at', '<=', $cutoff)
            ->get();

        foreach ($sessions as $session) {
            $session->update([
                'logout_at' => now(),
                'logout_reason' => 'forced_logout_by_job',
            ]);

            Log::info("[ForceLogoutInactiveUsersJob] 🔒 Sesión cerrada por inactividad. UserLogin ID: {$session->id}");
        }
    }
}
