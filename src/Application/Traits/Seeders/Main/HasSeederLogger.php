<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\Seeders\Main;

/**
 * Trait para emitir logs desde seeders compatibles con Artisan o CLI.
 */
trait HasSeederLogger
{
    protected function log(string $message): void
    {
        if (property_exists($this, 'command') && $this->command instanceof \Illuminate\Console\Command) {
            try {
                $this->command->line($message);
                return;
            } catch (\Throwable) {
                // fallback
            }
        }

        echo strip_tags($message) . PHP_EOL;
    }
}
