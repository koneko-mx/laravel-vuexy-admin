<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Console\Commands\Notifications;

use Illuminate\Console\Command;
use Koneko\VuexyAdmin\Models\DeviceToken;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'vuexy:tokens:prune')]
class VuexyDeviceTokenPruneCommand extends Command
{
    protected $signature = 'vuexy:tokens:prune {--days=30}';

    public function handle()
    {
        $threshold = now()->subDays((int) $this->option('days'));

        $count = DeviceToken::where('last_used_at', '<', $threshold)
            ->orWhere('is_active', false)
            ->delete();

        $this->info("🧹 $count tokens eliminados por inactividad.");
    }
}
