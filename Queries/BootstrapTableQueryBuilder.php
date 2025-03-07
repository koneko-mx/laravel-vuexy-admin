<?php

namespace Koneko\VuexyAdmin\Queries;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

abstract class BootstrapTableQueryBuilder
{
    protected $query;
    protected $request;
    protected $config;

    public function __construct(Request $request, array $config)
    {
        $this->request = $request;
        $this->config = $config;
        $this->query = DB::table($config['table']);

        $this->applyJoins();
        $this->applyFilters();
    }

    protected function applyJoins()
    {
        if (!empty($this->config['joins'])) {
            foreach ($this->config['joins'] as $join) {
                $type = $join['type'] ?? 'join';

                $this->query->{$type}($join['table'], function($joinObj) use ($join) {
                    $joinObj->on($join['first'], '=', $join['second']);

                    // Soporte para AND en ON, si está definidio
                    if (!empty($join['and'])) {
                        foreach ((array) $join['and'] as $andCondition) {
                            // 'sat_codigo_postal.c_estado = sat_localidad.c_estado'
                            $parts = explode('=', $andCondition);

                            if (count($parts) === 2) {
                                $left = trim($parts[0]);
                                $right = trim($parts[1]);

                                $joinObj->whereRaw("$left = $right");
                            }
                        }
                    }
                });
            }
        }
    }

    protected function applyFilters()
    {
        if (!empty($this->config['filters'])) {
            foreach ($this->config['filters'] as $filter => $column) {
                if ($this->request->filled($filter)) {
                    $this->query->where($column, 'LIKE', '%' . $this->request->input($filter) . '%');
                }
            }
        }
    }

    protected function applyGrouping()
    {
        if (!empty($this->config['group_by'])) {
            $this->query->groupBy($this->config['group_by']);
        }
    }

    public function getJson()
    {
        $this->applyGrouping();

        // Calcular total de filas antes de aplicar paginación
        $total = DB::select("SELECT COUNT(*) as num_rows FROM (" . $this->query->selectRaw('0')->toSql() . ") as items", $this->query->getBindings())[0]->num_rows;

        // Para ver la sentencia SQL (con placeholders ?)
        //dump($this->query->toSql()); dd($this->query->getBindings());

        // Aplicar orden, paginación y selección de columnas
        $this->query
            ->select($this->config['columns'])
            ->when($this->request->input('sort'), function ($query) {
                $query->orderBy($this->request->input('sort'), $this->request->input('order', 'asc'));
            })
            ->when($this->request->input('offset'), function ($query) {
                $query->offset($this->request->input('offset'));
            })
            ->limit($this->request->input('limit', 10));

        // Obtener resultados y limpiar los datos antes de enviarlos
        $rows = $this->query->get()->map(function ($item) {
            return collect($item)
                ->reject(fn($val) => is_null($val) || $val === '') // Eliminar valores nulos o vacíos
                ->map(fn($val) => is_numeric($val) ? (float) $val : $val) // Convertir números correctamente
                ->toArray();
        });

        return response()->json([
            "total" => $total,
            "rows" => $rows,
        ]);
    }
}
