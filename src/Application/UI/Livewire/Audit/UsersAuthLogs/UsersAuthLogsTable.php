<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\UI\Livewire\Audit\UsersAuthLogs;

use Koneko\VuexyAdmin\Application\UX\ConfigBuilders\Users\UserLoginTableConfigBuilder;
use Koneko\VuexyAdmin\Support\Livewire\Components\Table\AbstractTableComponent;

class UsersAuthLogsTable extends AbstractTableComponent
{
    protected function configBuilderClass(): ?string
    {
        return UserLoginTableConfigBuilder::class;
    }

    /**
     * Vista Blade que debe renderizar este componente.
     */
    protected function viewPath(): string
    {
       return 'vuexy-admin::livewire.audit.users-auth-logs.table-index';
    }
}
