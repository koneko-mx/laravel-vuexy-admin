<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Queries;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

/**
 * Clase base moderna para construir consultas de Bootstrap Table
 * compatible con el nuevo sistema ERP basado en TableConfigBuilder.
 *
 * Permite usar `getIndexBaseQuery()` directamente desde las clases
 * extendidas de `AbstractTableConfigBuilder`.
 */
class BootstrapTableQueryBuilder
{
    /** @var Request */
    protected Request $request;

    /** @var Builder */
    protected Builder $query;

    /** @var array */
    protected array $config;

    /**
     * Constructor principal.
     *
     * @param Request $request
     * @param Builder $baseQuery
     * @param array $config
     */
    public function __construct(Request $request, Builder $baseQuery, array $config)
    {
        $this->request = $request;
        $this->query   = $baseQuery;
        $this->config  = $config;

        $this->applyJoins();
        $this->applyFilters();
    }

    /**
     * Aplica los joins definidos en la configuración.
     */
    protected function applyJoins(): void
    {
        foreach ($this->config['joins'] ?? [] as $join) {
            if (!is_array($join) || count($join) < 4) {
                throw new \InvalidArgumentException('JOIN mal formado: ' . json_encode($join));
            }

            [$table, $first, $operator, $second] = $join;
            $extras = $join[4] ?? [];

            $type = $extras['type'] ?? (
                str_contains(strtolower($table), 'left') ? 'leftJoin' : 'join'
            );

            $this->query->{$type}($table, function ($joinObj) use ($first, $operator, $second, $extras) {
                $joinObj->on($first, $operator, $second);

                if (!empty($extras['and']) && is_array($extras['and'])) {
                    foreach ($extras['and'] as $condition) {
                        $joinObj->whereRaw($condition);
                    }
                }
            });
        }
    }

    /**
     * Aplica filtros definidos por configuración y request.
     */
    protected function applyFilters(): void
    {
        if (!empty($this->config['filters'])) {
            foreach ($this->config['filters'] as $filter => $columns) {

                if ($filter === 'search' && $this->request->filled('search')) {
                    $searchValue = $this->request->input('search');

                    $this->query->where(function ($query) use ($columns, $searchValue) {
                        foreach ($columns as $column) {
                            $query->orWhere($column, 'LIKE', "%{$searchValue}%");
                        }
                    });

                } elseif ($this->request->filled($filter)) {
                    $column = is_array($columns) ? $columns[0] : $columns;

                    $this->query->where($column, 'LIKE', "%{$this->request->input($filter)}%");
                }
            }
        }
    }

    /**
     * Aplica agrupaciones si se especificaron.
     */
    protected function applyGrouping(): void
    {
        if (!empty($this->config['group_by'])) {
            $this->query->groupBy($this->config['group_by']);
        }
    }

    /**
     * Ejecuta la consulta con paginación, ordenamiento y devuelve el JSON
     * compatible con Bootstrap Table.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getJson(): \Illuminate\Http\JsonResponse
    {
        $this->applyGrouping();

        $baseQuery = clone $this->query;
        $baseQuery->selectRaw('1'); // ← evita select *

        $total = DB::table(DB::raw("({$baseQuery->toSql()}) as sub"))
            ->setBindings($baseQuery->getBindings())
            ->count();

        $total = $baseQuery->count();

        // Paginar resultados reales
        $this->query
            ->select($this->config['columns'])
            ->when($this->request->input('sort'), function ($query) {
                $query->orderBy($this->request->input('sort'), $this->request->input('order', 'asc'));
            })
            ->when($this->request->input('offset'), fn($q) => $q->offset($this->request->input('offset')))
            ->limit($this->request->input('limit', 10));

        $rows = $this->query->toBase()->get()->map(function ($item) {
            return (array) $item; // ← convierte stdClass en array sin perder columnas
        });

        return response()->json([
            'total' => $total,
            'rows' => $rows,
        ]);
    }

}
