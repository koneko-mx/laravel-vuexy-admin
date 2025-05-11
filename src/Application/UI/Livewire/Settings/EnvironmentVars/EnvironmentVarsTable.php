<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UI\Livewire\Settings\EnvironmentVars;

use Koneko\VuexyAdmin\Application\UX\ConfigBuilders\System\EnvironmentVarsTableConfigBuilder;
use Koneko\VuexyAdmin\Support\Livewire\Components\Table\AbstractTableComponent;

class EnvironmentVarsTable extends AbstractTableComponent
{
    protected function configBuilderClass(): ?string
    {
        return EnvironmentVarsTableConfigBuilder::class;
    }

    /**
     * Vista Blade que debe renderizar este componente.
     */
    protected function viewPath(): string
    {
       return 'vuexy-admin::livewire.settings.environment-vars.table-index';
    }
}
