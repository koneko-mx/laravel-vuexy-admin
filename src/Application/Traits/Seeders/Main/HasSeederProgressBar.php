<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\Seeders\Main;

/**
 * Trait para manejar barras de progreso CLI desde seeders.
 */
trait HasSeederProgressBar
{
    protected function startProgress(int $total = 0): void
    {
        if ($this->canUseProgress() && $total > 0) {
            $this->command->getOutput()->progressStart($total);
        }
    }

    protected function advanceProgress(): void
    {
        if ($this->canUseProgress()) {
            $this->command->getOutput()->progressAdvance();
        }
    }

    protected function finishProgress(): void
    {
        if ($this->canUseProgress()) {
            $this->command->getOutput()->progressFinish();
        }
    }

    protected function canUseProgress(): bool
    {
        return property_exists($this, 'command')
            && $this->command instanceof \Illuminate\Console\Command
            && method_exists($this->command, 'getOutput');
    }
}
