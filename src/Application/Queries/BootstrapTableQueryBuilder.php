<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Application\Queries;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\DB;

/**
 * BootstrapTableQueryBuilder – Versión mejorada
 * -------------------------------------------------------------
 * Objetivo: ofrecer un motor robusto, flexible y simple para
 * construir consultas tipo "data table" con filtros, joins,
 * agrupado, orden y paginación seguros.
 *
 * CONFIG ESPERADA (ejemplos):
 * $config = [
 *   'columns'      => [ 'table.col', DB::raw('... as alias'), ... ], // usado cuando NO hay grouping
 *   'joins'        => [
 *      // Formato legacy (numérico)
 *      ['users as u', 'posts.user_id', '=', 'u.id', ['type' => 'leftJoin', 'and' => ["u.active = 1"]]],
 *      // Formato asociativo
 *      [
 *        'table' => 'users as u',
 *        'first' => 'posts.user_id', 'operator' => '=', 'second' => 'u.id',
 *        'type'  => 'leftJoin',
 *        'and'   => function($join){ $join->where('u.active', 1); },
 *      ],
 *   ],
 *   'filters'      => [
 *      // DSL de filtros
 *      'search' => ['op' => 'like_any', 'param' => 'search', 'columns' => ['p.title','p.slug']],
 *      'status' => ['column' => 'p.status', 'op' => 'in', 'type' => 'string'],
 *      'date'   => ['column' => 'p.published_at', 'op' => 'between', 'type' => 'date'],
 *      'custom' => ['closure' => fn($q,$v) => $q->whereRaw('JSON_VALID(meta)')],
 *   ],
 *   'grouping'     => [
 *      'by'         => ['p.status'],
 *      'aggregates' => [ 'total' => DB::raw('COUNT(*)'), 'last' => DB::raw('MAX(p.updated_at)') ],
 *      'having'     => [ ['column' => 'total', 'op' => '>', 'value' => 0] ], // o closures
 *   ],
 *   'allowed_sort' => [ // whitelist de ordenamiento
 *      // lista simple => mismos nombres que vendrán en ?sort=
 *      'title','status','updated_at','total','last',
 *      // o map alias=>columna/expresión
 *      // 'title' => 'p.title', 'total' => 'total',
 *   ],
 *   'default_sort' => ['column' => 'id', 'order' => 'desc'],
 *   'max_limit'    => 1000,
 *   'default_limit'=> 10,
 * ];
 */
class BootstrapTableQueryBuilder
{
    protected Request $request;
    protected Builder $query;
    protected array $config;

    public function __construct(Request $request, Builder $baseQuery, array $config)
    {
        $this->request = $request;
        $this->query   = $baseQuery;
        $this->config  = $config;

        $this->applyJoins();
        $this->applyFilters();
    }

    /**
     * JOINS robustos: soporta formato legacy (numérico) y asociativo.
     * Extras:
     *  - type: join|leftJoin|rightJoin
     *  - and: string|array|Closure => condiciones adicionales sobre el join
     */
    protected function applyJoins(): void
    {
        foreach ($this->config['joins'] ?? [] as $def) {
            [$table, $first, $operator, $second, $extras] = $this->normalizeJoinDefinition($def);

            $type = in_array($extras['type'] ?? 'join', ['join','leftJoin','rightJoin'], true)
                ? $extras['type'] : 'join';

            $this->query->{$type}($table, function ($join) use ($first, $operator, $second, $extras) {
                $join->on($first, $operator, $second);

                if (!array_key_exists('and', $extras) || $extras['and'] === null) {
                    return;
                }

                $and = $extras['and'];
                // string => whereRaw
                if (is_string($and)) {
                    $join->whereRaw($and);
                    return;
                }
                // array => múltiples whereRaw o where(col,op,val)
                if (is_array($and)) {
                    foreach ($and as $cond) {
                        if (is_string($cond)) {
                            $join->whereRaw($cond);
                        } elseif (is_array($cond) && count($cond) >= 2) {
                            // [col, val] ó [col, op, val]
                            [$col, $opOrVal, $val] = [$cond[0], $cond[1] ?? null, $cond[2] ?? null];
                            if ($val === null) {
                                $join->where($col, '=', $opOrVal);
                            } else {
                                $join->where($col, $opOrVal, $val);
                            }
                        }
                    }
                    return;
                }
                // Closure personalizada
                if ($and instanceof Closure) {
                    $and($join);
                }
            });
        }
    }

    /** @return array{0:string,1:string,2:string,3:string,4:array} */
    protected function normalizeJoinDefinition(array $def): array
    {
        // legacy numérico: [table, first, operator, second, extras?]
        if (isset($def[0], $def[1], $def[2], $def[3])) {
            $table    = (string) $def[0];
            $first    = (string) $def[1];
            $operator = (string) $def[2];
            $second   = (string) $def[3];
            $extras   = (array)  ($def[4] ?? []);
            return [$table, $first, $operator, $second, $extras];
        }

        // asociativo: keys => table, first, operator, second, type, and
        if (!isset($def['table'], $def['first'], $def['operator'], $def['second'])) {
            throw new \InvalidArgumentException('JOIN mal formado: ' . json_encode($def));
        }
        return [
            (string) $def['table'],
            (string) $def['first'],
            (string) $def['operator'],
            (string) $def['second'],
            [
                'type' => $def['type'] ?? null,
                'and'  => $def['and']  ?? null,
            ],
        ];
    }

    /**
     * Aplica filtros declarativos (DSL) y closures custom.
     */
    protected function applyFilters(): void
    {
        $filters = $this->config['filters'] ?? [];
        if (!$filters) return;

        foreach ($filters as $key => $def) {
            // Closure directa
            if (isset($def['closure']) && is_callable($def['closure'])) {
                $param = $def['param'] ?? $key;
                if ($this->request->filled($param)) {
                    $value = $this->request->input($param);
                    ($def['closure'])($this->query, $this->normalize($value, $def['type'] ?? null), $this->request);
                }
                continue;
            }

            $op   = strtolower((string) ($def['op'] ?? '='));
            $type = (string) ($def['type'] ?? 'string');

            // like_any: búsqueda global multi-columna
            if ($op === 'like_any') {
                $param = $def['param'] ?? $key;
                if ($this->request->filled($param)) {
                    $search = (string) $this->request->input($param);
                    $cols   = (array) ($def['columns'] ?? []);
                    if ($cols) {
                        $this->query->where(function ($q) use ($cols, $search) {
                            foreach ($cols as $c) {
                                $q->orWhere($c, 'like', "%{$search}%");
                            }
                        });
                    }
                }
                continue;
            }

            $param  = $def['param'] ?? $key;
            $column = $def['column'] ?? null;

            // BETWEEN (from/to)
            if ($op === 'between') {
                $fromKey = is_array($param) ? ($param['from'] ?? 'from') : ($param . '_from');
                $toKey   = is_array($param) ? ($param['to']   ?? 'to')   : ($param . '_to');

                $hasFrom = $this->request->filled($fromKey);
                $hasTo   = $this->request->filled($toKey);
                if (!$hasFrom && !$hasTo) continue;

                $from = $hasFrom ? $this->normalize($this->request->input($fromKey), $type) : null;
                $to   = $hasTo   ? $this->normalize($this->request->input($toKey),   $type) : null;

                if ($from !== null && $to !== null) {
                    $this->query->whereBetween($column, [$from, $to]);
                } elseif ($from !== null) {
                    $this->query->where($column, '>=', $from);
                } elseif ($to !== null) {
                    $this->query->where($column, '<=', $to);
                }
                continue;
            }

            // Si el parámetro no viene, saltamos
            if (!$this->request->filled($param)) continue;

            // IN (array o CSV)
            if ($op === 'in') {
                $raw = $this->request->input($param);
                $arr = is_array($raw) ? $raw : array_filter(array_map('trim', explode(',', (string) $raw)));
                $arr = array_map(fn($v) => $this->normalize($v, $type), $arr);
                if ($arr) $this->query->whereIn($column, $arr);
                continue;
            }

            // JSON_CONTAINS para cualquiera de los valores
            if ($op === 'json_contains_any') {
                $raw = $this->request->input($param);
                $arr = is_array($raw) ? $raw : array_filter(array_map('trim', explode(',', (string) $raw)));
                if ($arr) {
                    $this->query->where(function ($q) use ($column, $arr) {
                        foreach ($arr as $val) {
                            $q->orWhereRaw("JSON_CONTAINS({$column}, json_array(?))", [$val]);
                        }
                    });
                }
                continue;
            }

            // Operadores simples (=, <>, >, >=, <, <=, like)
            $value = $this->normalize($this->request->input($param), $type);
            if ($op === 'like') {
                $this->query->where($column, 'like', "%{$value}%");
            } else {
                $this->query->where($column, $op, $value);
            }
        }
    }

    /**
     * Agrupado + agregados + having. Si se define, el SELECT se compone aquí.
     */
    protected function applyGrouping(): void
    {
        $g = $this->config['grouping'] ?? null;
        if (!$g) return;

        $by   = (array)($g['by'] ?? []);
        $aggs = (array)($g['aggregates'] ?? []);

        if ($by) {
            $this->query->groupBy($by);
        }

        $selects = [];

        // columnas de agrupación (pueden ser strings o Expression)
        foreach ($by as $col) {
            if ($col instanceof \Illuminate\Database\Query\Expression) {
                $selects[] = $col; // Expression se pasa tal cual
            } else {
                $selects[] = (string)$col; // columna normal
            }
        }

        // agregados con alias seguro
        $grammar = $this->query->getConnection()->getQueryGrammar();

        foreach ($aggs as $alias => $expr) {
            if ($expr instanceof \Illuminate\Database\Query\Expression) {
                $sql = $expr->getValue($grammar); // lee el SQL del Expression
            } else {
                $sql = (string)$expr;
            }

            // si no trae AS <alias>, agrégalo
            if (!preg_match('/\bas\s+'.preg_quote((string)$alias, '/').'\b/i', $sql)) {
                $sql .= ' AS ' . $alias;
            }

            $selects[] = DB::raw($sql);
        }

        if ($selects) {
            $this->query->select($selects);
        }

        // HAVING
        foreach (($g['having'] ?? []) as $h) {
            if (is_array($h) && isset($h['column'])) {
                $col = $h['column']; $op = $h['op'] ?? '>'; $val = $h['value'] ?? 0;
                $this->query->having($col, $op, $val);
            } elseif ($h instanceof \Closure) {
                $h($this->query);
            }
        }
    }


    /** Normalización de tipos para filtros */
    protected function normalize($v, ?string $type)
    {
        return match ($type) {
            'int'       => $v === null ? null : (int) $v,
            'float'     => $v === null ? null : (float) $v,
            'bool'      => in_array(strtolower((string) $v), ['1','true','yes','on'], true),
            'date'      => $v ? date('Y-m-d', strtotime((string) $v)) : null,
            'datetime'  => $v ? date('Y-m-d H:i:s', strtotime((string) $v)) : null,
            'array'     => is_array($v) ? $v : array_filter(array_map('trim', explode(',', (string) $v))),
            default     => $v,
        };
    }

    /** Orden seguro con whitelist */
    protected function applySorting(bool $isGrouped): void
    {
        $requestedField = (string) $this->request->input('sort', '');
        $requestedOrder = strtolower((string) $this->request->input('order', 'asc'));
        $requestedOrder = in_array($requestedOrder, ['asc','desc'], true) ? $requestedOrder : 'asc';

        $allowed = $this->config['allowed_sort'] ?? [];
        $map     = $this->normalizeAllowedSort($allowed, $isGrouped);

        if ($requestedField && isset($map[$requestedField])) {
            $this->query->orderBy($map[$requestedField], $requestedOrder);
            return;
        }

        // Default sort
        $default = $this->config['default_sort'] ?? null;
        if ($default && isset($default['column'])) {
            $col = (string) $default['column'];
            $ord = in_array(strtolower((string) ($default['order'] ?? 'desc')), ['asc','desc'], true)
                ? strtolower((string) $default['order']) : 'desc';
            $this->query->orderBy($col, $ord);
        }
    }

    /** @return array<string,string> map alias => columna/alias seguro */
    protected function normalizeAllowedSort(array $allowed, bool $isGrouped): array
    {
        $map = [];
        // Acepta lista simple ["title","status"] o mapa ["title"=>"p.title"]
        foreach ($allowed as $k => $v) {
            if (is_int($k)) {
                // lista simple: el valor es el alias/col directo en el SELECT
                $map[(string) $v] = (string) $v;
            } else {
                $map[(string) $k] = (string) $v;
            }
        }

        // Si hay grouping y existen agregados con alias, permite ordenar por esos alias
        if ($isGrouped && !empty($this->config['grouping']['aggregates'])) {
            foreach ($this->config['grouping']['aggregates'] as $alias => $_expr) {
                $alias = (string) $alias;
                if (!isset($map[$alias])) {
                    $map[$alias] = $alias; // alias presente en SELECT
                }
            }
        }

        return $map;
    }

    /** Paginación con límites razonables */
    protected function applyPagination(): void
    {
        $max    = (int) ($this->config['max_limit']     ?? 1000);
        $def    = (int) ($this->config['default_limit'] ?? 10);
        $limit  = (int) $this->request->input('limit', $def);
        $offset = (int) $this->request->input('offset', 0);

        $limit  = $limit <= 0 ? $def : min($limit, $max);
        $offset = max(0, $offset);

        $this->query->when($offset, fn($q) => $q->offset($offset))
                    ->limit($limit);
    }

    protected function isGrouped(): bool
    {
        $g = $this->config['grouping'] ?? null;
        return !empty($g) && (!empty($g['by']) || !empty($g['aggregates']));
    }

    protected function applySelectIfNeeded(bool $isGrouped): void
    {
        if ($isGrouped) return; // En grouping el SELECT ya se aplica en applyGrouping()

        $cols = $this->config['columns'] ?? [];
        if ($cols) {
            $this->query->select($cols);
        }
    }

    protected function totalCount(bool $isGrouped): int
    {
        if ($isGrouped) {
            // Cuenta grupos: subquery sobre la consulta ya agrupada (sin order/limit)
            $countQuery = clone $this->query;
            $sub = DB::table(DB::raw('(' . $countQuery->toSql() . ') as sub'))
                    ->setBindings($countQuery->getBindings());
            return (int) $sub->count();
        }

        // Sin agrupado: count simple (evitando SELECT pesado)
        $countQuery = clone $this->query;
        return (int) $countQuery->selectRaw('1')->count();
    }

    protected function collectAppliedFilters(): array
    {
        $out = [];
        foreach (($this->config['filters'] ?? []) as $key => $def) {
            $param = $def['param'] ?? $key;
            if (is_array($param)) {
                $vals = [];
                foreach ($param as $k) {
                    if ($this->request->filled($k)) $vals[$k] = $this->request->input($k);
                }
                if ($vals) $out[$key] = $vals;
                continue;
            }
            if ($this->request->filled($param)) {
                $out[$key] = $this->request->input($param);
            }
        }
        return $out;
    }

    /**
     * Ejecuta la consulta con paginación, orden y devuelve JSON para Bootstrap Table.
     */
    public function getJson(): JsonResponse
    {
        $isGrouped = $this->isGrouped();
        if ($isGrouped) {
            $this->applyGrouping();
        }

        // SELECT (no agrupado)
        $this->applySelectIfNeeded($isGrouped);

        // TOTAL
        $total = $this->totalCount($isGrouped);

        // ORDER + LIMIT/OFFSET
        $this->applySorting($isGrouped);
        $this->applyPagination();

        // DATA
        $rows = $this->query->toBase()->get()->map(fn($i) => (array) $i);

        // META (opcional, útil para depurar/UX)
        $meta = [
            'grouped'  => $isGrouped,
            'sort'     => [
                'field' => (string) $this->request->input('sort', ''),
                'order' => (string) $this->request->input('order', ''),
            ],
            'limit'    => (int) $this->request->input('limit', $this->config['default_limit'] ?? 10),
            'offset'   => (int) $this->request->input('offset', 0),
            'filters'  => $this->collectAppliedFilters(),
        ];

        return response()->json([
            'total' => $total,
            'rows'  => $rows,
            'meta'  => $meta,
        ]);
    }
}
