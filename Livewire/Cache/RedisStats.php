<?php

namespace Koneko\VuexyAdmin\Livewire\Cache;

use Livewire\Component;
use Koneko\VuexyAdmin\Services\CacheManagerService;

class RedisStats extends Component
{
    private $driver       = 'redis';
    private $targetNotify = "#redis-stats-card .notification-container";

    public $redisStats = [];

    protected $listeners = ['reloadRedisStatsEvent' => 'reloadCacheStats'];

    public function mount()
    {
        $this->reloadCacheStats(false);
    }

    public function reloadCacheStats($notify = true)
    {
        $cacheManagerService = new CacheManagerService($this->driver);

        $redisStats = $cacheManagerService->getRedisStats();

        $this->redisStats = $redisStats['info'];

        if ($notify) {
            $this->dispatch(
                'notification',
                target: $this->targetNotify,
                type: $redisStats['status'],
                message: $redisStats['message']
            );
        }
    }

    public function clearCache()
    {
        $cacheManagerService = new CacheManagerService($this->driver);

        $message = $cacheManagerService->clearCache();

        $this->reloadCacheStats(false);

        $this->dispatch(
            'notification',
            target: $this->targetNotify,
            type: $message['status'],
            message: $message['message'],
        );

        $this->dispatch('reloadCacheStatsEvent', notify: false);
        $this->dispatch('reloadSessionStatsEvent', notify: false);
        $this->dispatch('reloadCacheFunctionsStatsEvent', notify: false);
    }

    public function render()
    {
        return view('vuexy-admin::livewire.cache.redis-stats');
    }
}
