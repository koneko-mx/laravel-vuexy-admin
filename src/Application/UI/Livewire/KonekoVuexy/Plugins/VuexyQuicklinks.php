<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UI\Livewire\KonekoVuexy\Plugins;

use Illuminate\Support\Facades\Route;
use Koneko\VuexyAdmin\Application\UX\Navbar\VuexyQuicklinksBuilderService;
use Koneko\VuexyAdmin\Support\Traits\Livewire\Notifications\HandlesAsyncNotifications;
use Livewire\Component;

class VuexyQuicklinks extends Component
{
    use HandlesAsyncNotifications;

    public $vuexyQuickLinks;

    public bool $isRouteAllowed = false;

    public string $currentRouteId = '';
    public string $slug = '';

    public function mount()
    {
        $this->slug           = Route::current()->parameter('slug') ?? '';
        $this->currentRouteId = $this->getCurrentRouteId();
        $this->isRouteAllowed = app(VuexyQuicklinksBuilderService::class)->isRouteAllowed($this->currentRouteId);

        $this->loadQuickLinks();
    }

    public function add(): void
    {
        if (!$this->isRouteAllowed) return;

        app(VuexyQuicklinksBuilderService::class)->addRoute($this->currentRouteId);

        $this->loadQuickLinks();
        $this->notify('Atajo agregado correctamente', 'success');
        $this->refreshTooltips();
    }

    public function remove(): void
    {
        app(VuexyQuicklinksBuilderService::class)->removeRoute($this->currentRouteId);

        $this->loadQuickLinks();
        $this->notify('Atajo removido correctamente', 'warning');
        $this->refreshTooltips();
    }

    private function getCurrentRouteId(): string
    {
        return $this->slug ? "slug:{$this->slug}" : Route::currentRouteName();
    }


    public function loadQuickLinks(?string $routeId = null): void
    {
        $routeId ??= $this->currentRouteId;

        $quickLinks = app(VuexyQuicklinksBuilderService::class)->getUserQuicklinks();

        $quickLinks['current_page_in_list'] = collect($quickLinks['rows'] ?? [])
            ->flatten(1)
            ->contains(fn ($item) => ($item['route'] ?? null) === $routeId);

        $this->vuexyQuickLinks = $quickLinks;
    }

    public function refreshTooltips(): void
    {
        $this->dispatch('refresh-tooltips');
    }

    /**
     * Vista Blade que debe renderizar este componente.
     */
    public function render()
    {
        return view('vuexy-admin::livewire.navbar.vuexy-quicklinks');
    }
}
