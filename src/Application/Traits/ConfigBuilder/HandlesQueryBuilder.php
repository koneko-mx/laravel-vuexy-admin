<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Traits\ConfigBuilder;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Koneko\VuexyAdmin\Application\Queries\BootstrapTableQueryBuilder;

/**
 * Trait para manejar el constructor de consultas.
 */
trait HandlesQueryBuilder
{
    use HandlesIndexColumns;

    public function getIndexBaseQuery(): Builder
    {
        return $this->getModelClass()::query();
    }

    public function getQueryBuilder(Request $request): BootstrapTableQueryBuilder
    {
        return new BootstrapTableQueryBuilder($request, $this->getIndexBaseQuery(), [
            'table'    => $this->getTableName(),
            'columns'  => $this->getIndexColumns(),
            'joins'    => $this->getIndexJoins(),
            'filters'  => $this->getIndexFilters(),
            'group_by' => $this->getIndexGroupBy(),
        ]);
    }
}
