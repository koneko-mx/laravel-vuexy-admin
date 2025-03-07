<?php

namespace Koneko\VuexyAdmin\Livewire\Cache;

use Livewire\Component;
use Koneko\VuexyAdmin\Services\CacheConfigService;
use Koneko\VuexyAdmin\Services\CacheManagerService;

class CacheStats extends Component
{
    private $targetNotify = "#cache-stats-card .notification-container";

    public $cacheConfig = [];
    public $cacheStats = [];

    protected $listeners = ['reloadCacheStatsEvent' => 'reloadCacheStats'];

    public function mount(CacheConfigService $cacheConfigService)
    {
        $this->cacheConfig = $cacheConfigService->getConfig();

        $this->reloadCacheStats(false);
    }

    public function reloadCacheStats($notify = true)
    {
        $cacheManagerService = new CacheManagerService();

        $this->cacheStats = $cacheManagerService->getCacheStats();

        if ($notify) {
            $this->dispatch(
                'notification',
                target: $this->targetNotify,
                type: $this->cacheStats['status'],
                message: $this->cacheStats['message']
            );
        }
    }

    public function clearCache()
    {
        $cacheManagerService = new CacheManagerService();

        $message = $cacheManagerService->clearCache();

        $this->reloadCacheStats(false);

        $this->dispatch(
            'notification',
            target: $this->targetNotify,
            type: $message['status'],
            message: $message['message'],
        );

        $this->dispatch('reloadRedisStatsEvent', notify: false);
        $this->dispatch('reloadMemcachedStatsEvent', notify: false);
        $this->dispatch('reloadCacheFunctionsStatsEvent', notify: false);
    }

    public function render()
    {
        return view('vuexy-admin::livewire.cache.cache-stats');
    }
}
