<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UI\Livewire\Tools\Cache;

use Koneko\VuexyAdmin\Application\Cache\CacheConfigService;
use Koneko\VuexyAdmin\Application\Cache\LaravelCacheManager;
use Livewire\Component;

class CacheStatsCard extends Component
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
        $cacheManagerService = new LaravelCacheManager();

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
        $cacheManagerService = new LaravelCacheManager();

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
        return view('vuexy-admin::livewire.tools.cache.cache-stats-card');
    }
}
