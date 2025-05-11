<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UI\Livewire\Tools\Cache;

use Livewire\Component;
use Koneko\VuexyAdmin\Application\Cache\{CacheConfigService,KonekoSessionManager};

class SessionStatsCard extends Component
{
    private $targetNotify = "#session-stats-card .notification-container";

    public $cacheConfig = [];
    public $sessionStats = [];

    protected $listeners = ['reloadSessionStatsEvent' => 'reloadSessionStats'];

    public function mount(CacheConfigService $cacheConfigService)
    {
        $this->cacheConfig = $cacheConfigService->getConfig();
        $this->reloadSessionStats(false);
    }

    public function reloadSessionStats($notify = true)
    {
        $sessionManagerService = new KonekoSessionManager();

        $this->sessionStats = $sessionManagerService->getSessionStats();

        if ($notify) {
            $this->dispatch(
                'notification',
                target: $this->targetNotify,
                type: $this->sessionStats['status'],
                message: $this->sessionStats['message']
            );
        }
    }

    public function clearSessions()
    {
        $sessionManagerService = new KonekoSessionManager();

        $message = $sessionManagerService->clearSessions();

        $this->reloadSessionStats(false);

        $this->dispatch(
            'notification',
            target: $this->targetNotify,
            type: $message['status'],
            message: $message['message'],
        );

        $this->dispatch('reloadRedisStatsEvent', notify: false);
        $this->dispatch('reloadMemcachedStatsEvent', notify: false);
    }

    public function render()
    {
        return view('vuexy-admin::livewire.tools.cache.session-stats-card');
    }
}
