<?php

namespace Koneko\VuexyAdmin\Livewire\Cache;

use Livewire\Component;
use Koneko\VuexyAdmin\Services\CacheConfigService;
use Koneko\VuexyAdmin\Services\SessionManagerService;

class SessionStats extends Component
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
        $sessionManagerService = new SessionManagerService();

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
        $sessionManagerService = new SessionManagerService();

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
        return view('vuexy-admin::livewire.cache.session-stats');
    }
}
