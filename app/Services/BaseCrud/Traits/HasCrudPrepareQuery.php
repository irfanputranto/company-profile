<?php

namespace App\Services\BaseCrud\Traits;

use App\Models\Service\Appointment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Throwable;

trait HasCrudPrepareQuery
{
    public $disableOrderList = false;

    public $searchKeyword = 'q';

    public $customSearchable = [
        // [
        //     'morph' => 'refable',
        //     'class' => Appointment::class,
        //     'searchable' => []
        // ]
    ];

    public function __prepareQuerySearchAbleList()
    {
        $query = $this->query;

        $request = $this->requestData;

        if ($q = $request->query($this->searchKeyword)) {
            $query->where(function ($qq) use ($q) {
                $lower = 'LOWER';
                $like = 'like';
                foreach ($this->searchAble as $v) {
                    if (Str::contains($v, '.')) {
                        $ex = explode('.', $v);

                        $rel = implode('.', array_values(array_slice($ex, 0, count($ex) - 1)));

                        $qq->orWhereHas($rel, function ($qqq) use ($q, $ex, $lower, $like) {
                            $qqq->whereRaw(
                                $lower.'('.$ex[count($ex) - 1].') '.$like.' ?',
                                ['%'.strtolower($q).'%']
                            );
                        });
                    } else {
                        $qq->orWhereRaw($lower.'('.app($this->model)->getTable().'.'.$v.') '.$like.' ?', ['%'.strtolower($q).'%']);
                    }
                }
                $this->additionalSearchable($qq, $q);
            });
        }

        return $query;
    }

    public function __prepareQuerySearchAblePrint()
    {
        $query = $this->query;

        $request = $this->requestData;

        if ($q = $request->query($this->searchKeyword)) {
            $query->where(function ($qq) use ($q) {
                $lower = 'LOWER';
                $like = 'like';
                foreach ($this->searchAble as $v) {
                    if (Str::contains($v, '.')) {
                        $ex = explode('.', $v);

                        $rel = implode('.', array_values(array_slice($ex, 0, count($ex) - 1)));

                        $qq->orWhereHas($rel, function ($qqq) use ($q, $ex, $lower, $like) {
                            $qqq->whereRaw(
                                $lower.'('.$ex[count($ex) - 1].') '.$like.' ?',
                                ['%'.strtolower($q).'%']
                            );
                        });
                    } else {
                        $qq->orWhereRaw($lower.'('.app($this->model)->getTable().'.'.$v.') '.$like.' ?', ['%'.strtolower($q).'%']);
                    }
                }
                $this->additionalSearchable($qq, $q);
            });
        }

        return $query;
    }

    public function __prepareQueryOptionsList()
    {
        $query = $this->query;

        $request = $this->requestData;

        $options = $request->query('options');

        $search = [];
        $filter = [];
        $has = [];
        $doesntHave = [];

        $operations = [
            'equal' => '=',
            'not_equal' => '!=',
            'in' => 'IN',
            'not_in' => 'NOT IN',
            'less_then' => '<',
            'greater_than' => '>',
            'less_then_equal' => '<=',
            'greater_than_equal' => '>=',
            'is_null' => 'IS NULL',
            'is_not_null' => 'IS NOT NULL',
            'between' => 'BETWEEN',
            'not_between' => 'NOT BETWEEN',
        ];

        if (! empty($options) && is_array($options)) {
            foreach ($options as $o) {
                if (! is_string($o)) {
                    continue;
                }

                $x = explode(',', $o);
                $key = $x[0] ?? '';
                if ($key == 'search') {
                    $search[] = $x;
                }
                if ($key == 'filter') {
                    $filter[] = $x;
                }
                if ($key == 'has') {
                    $has[] = $x;
                }
                if ($key == 'doesntHave') {
                    $doesntHave[] = $x;
                }
            }
        }

        if (! empty($search)) {
            $query->where(function ($query) use ($search) {
                $lower = 'LOWER';
                $like = 'like';

                foreach ($search as $v) {

                    if (empty($v[1]) || ! isset($v[2]) || ! $this->isSafeFilterPath($v[1])) {
                        continue;
                    }

                    if (Str::contains($v[1], '.')) {
                        $ex = explode('.', $v[1]);
                        $rel = implode('.', array_values(array_slice($ex, 0, count($ex) - 1)));

                        if (! $this->isSafeRelationPath($rel)) {
                            continue;
                        }

                        $query->whereHas($rel, function ($query) use ($v, $ex, $lower, $like) {
                            $query->whereRaw(
                                $lower.'('.$ex[count($ex) - 1].') '.$like.' ?',
                                ['%'.strtolower($v[2]).'%']
                            );
                        });
                    } else {
                        $query->whereRaw($lower.'('.$v[1].') '.$like.' ?', ['%'.strtolower($v[2]).'%']);
                    }
                }
            });
        }

        if (! empty($filter)) {
            $query->where(function ($query) use ($filter, $operations) {

                foreach ($filter as $v) {

                    if (
                        empty($v[1])
                        || empty($v[2])
                        || empty($operations[$v[2]])
                        || ! $this->isSafeFilterPath($v[1])
                    ) {
                        continue;
                    }

                    $value = $v[3] ?? null;

                    if (Str::contains($v[1], '.')) {
                        $ex = explode('.', $v[1]);
                        $rel = implode('.', array_values(array_slice($ex, 0, count($ex) - 1)));

                        if (! $this->isSafeRelationPath($rel)) {
                            continue;
                        }

                        $query->whereHas($rel, function ($query) use ($v, $ex, $operations, $value) {
                            $field = $ex[count($ex) - 1];
                            $op = $operations[$v[2]];
                            if ($op == 'IN') {
                                $query->whereIn($field, explode('|', (string) $value));
                            } elseif ($op == 'NOT IN') {
                                $query->whereNotIn($field, explode('|', (string) $value));
                            } elseif ($op == 'IS NULL') {
                                $query->whereNull($field);
                            } elseif ($op == 'IS NOT NULL') {
                                $query->whereNotNull($field);
                            } elseif ($op == 'BETWEEN') {
                                $range = explode('|', (string) $value, 2);
                                if (count($range) === 2) {
                                    $query->whereBetween($field, $range);
                                }
                            } elseif ($op == 'NOT BETWEEN') {
                                $range = explode('|', (string) $value, 2);
                                if (count($range) === 2) {
                                    $query->whereNotBetween($field, $range);
                                }
                            } else {
                                $query->where($field, $op, $value);
                            }
                        });
                    } else {
                        $field = $v[1];
                        $op = $operations[$v[2]];
                        if ($op == 'IN') {
                            $query->whereIn(app($this->model)->getTable().'.'.$field, explode('|', (string) $value));
                        } elseif ($op == 'NOT IN') {
                            $query->whereNotIn(app($this->model)->getTable().'.'.$field, explode('|', (string) $value));
                        } elseif ($op == 'IS NULL') {
                            $query->whereNull(app($this->model)->getTable().'.'.$field);
                        } elseif ($op == 'IS NOT NULL') {
                            $query->whereNotNull(app($this->model)->getTable().'.'.$field);
                        } elseif ($op == 'BETWEEN') {
                            $range = explode('|', (string) $value, 2);
                            if (count($range) === 2) {
                                $query->whereBetween(app($this->model)->getTable().'.'.$field, $range);
                            }
                        } elseif ($op == 'NOT BETWEEN') {
                            $range = explode('|', (string) $value, 2);
                            if (count($range) === 2) {
                                $query->whereNotBetween(app($this->model)->getTable().'.'.$field, $range);
                            }
                        } else {
                            $query->where(app($this->model)->getTable().'.'.$field, $op, $value);
                        }
                    }
                }
            });
        }

        if (! empty($has)) {
            $query->where(function ($query) use ($has) {
                foreach ($has as $v) {
                    if (empty($v[1]) || ! $this->isSafeColumnPath($v[1]) || ! $this->isSafeRelationPath($v[1])) {
                        continue;
                    }
                    $query->has($v[1]);
                }
            });
        }

        if (! empty($doesntHave)) {
            $query->where(function ($query) use ($doesntHave) {
                foreach ($doesntHave as $v) {
                    if (empty($v[1]) || ! $this->isSafeColumnPath($v[1]) || ! $this->isSafeRelationPath($v[1])) {
                        continue;
                    }
                    $query->doesntHave($v[1]);
                }
            });
        }

        return $query;
    }

    public function additionalSearchable($query, $q)
    {
        foreach ($this->customSearchable as $data) {
            foreach ($data['searchable'] as $v) {
                $query->orWhereHasMorph($data['morph'], $data['class'], function ($qq) use ($q, $v) {
                    if (Str::contains($v, '.')) {
                        $ex = explode('.', $v);

                        $rel = implode('.', array_values(array_slice($ex, 0, count($ex) - 1)));

                        $qq->whereHas($rel, function ($qqq) use ($q, $ex) {
                            $qqq->whereRaw('LOWER('.$ex[count($ex) - 1].') like ?', ['%'.strtolower($q).'%']);
                        });
                    } else {
                        $qq->whereRaw('LOWER('.$v.') like ?', ['%'.strtolower($q).'%']);
                    }
                });
            }
        }
    }

    public function __prepareQueryListType()
    {
        $query = $this->query;

        $request = $this->requestData;

        if ($request->query('type') == 'pagination') {
            $query = $query->paginate($this->paginationPerPage);
            $this->__prepareListPaginationAppend($query);

            return $query;
        }

        return $query->get();
    }

    public function __prepareQueryPrintType()
    {
        $query = $this->query;

        return $query->get();
    }

    public function __prepareListPaginationAppend($query)
    {
        $request = $this->requestData;

        foreach ($request->query() as $key => $value) {
            $query->appends($key, $value);
        }

        return $query;
    }

    public function __prepareCacheResult()
    {
        $request = $this->requestData;

        if ($request->query('is_cache') == '1') {
            $key = $request->getRequestUri();

            $dt = Cache::get($key);
            if (! empty($dt)) {
                return $dt;
            }
        }
    }

    public function __prepareQueryRelationList()
    {
        $query = $this->query;

        foreach ($this->relationList as $value) {
            $query->with($value);
        }

        return $query;
    }

    public function __prepareQueryRelationPrint()
    {
        $query = $this->query;

        foreach ($this->relationPrint as $value) {
            $query->with($value);
        }

        return $query;
    }

    public function __prepareQueryList()
    {
        return $this->query;
    }

    public function __prepareQuerySortOrderList()
    {
        $query = $this->query;

        if ($this->disableOrderList) {
            return $query;
        }

        return $this->applySafeOrder(
            $query,
            $this->requestData->query('order_by'),
            $this->requestData->query('sort_by'),
        );
    }

    public function __prepareQuerySortOrderPrint()
    {
        $query = $this->query;

        return $this->applySafeOrder(
            $query,
            $this->requestData->query('order_by'),
            $this->requestData->query('sort_by'),
        );
    }

    public function __prepareQueryLimitList()
    {
        $query = $this->query;

        $request = $this->requestData;

        $type = $request->query('type');

        $limit = $request->query('limit');

        if (is_numeric($limit)) {
            $this->paginationPerPage = min(max((int) $limit, 1), 100);
            if ($type != 'pagination') {
                $query->limit($this->paginationPerPage);
            }
        }

        return $query;
    }

    public function __prepareQueryLimitPrint()
    {
        $query = $this->query;

        $request = $this->requestData;

        $limit = $request->integer('limit', 1000);
        $query->limit(min(max($limit, 1), 5000));

        return $query;
    }

    private function applySafeOrder($query, mixed $requestedColumn, mixed $requestedDirection)
    {
        $orderBy = $this->safeOrderColumn($requestedColumn) ?: $this->defaultOrder;
        $sortBy = $this->safeSortDirection($requestedDirection)
            ?? $this->safeSortDirection($this->defaultSort)
            ?? 'asc';

        if (! Str::contains($orderBy, '.')) {
            return $query->orderBy($orderBy, $sortBy);
        }

        $parts = explode('.', $orderBy);
        $relationsConstant = $this->model.'::relations';
        $relations = defined($relationsConstant) ? constant($relationsConstant) : [];
        $currentTable = app($this->model)->getTable();

        for ($i = 0; $i < count($parts) - 1; $i++) {
            $relation = implode('.', array_slice($parts, 0, $i + 1));
            $relationConfig = $relations[$relation] ?? null;

            if (! is_array($relationConfig) || empty($relationConfig['table']) || empty($relationConfig['field'])) {
                return $query->orderBy($this->defaultOrder, $sortBy);
            }

            $joinTable = $relationConfig['table'];
            $joinField = $relationConfig['field'];

            if (! empty($relationConfig['is_has'])) {
                $query->join($joinTable, "{$joinTable}.{$joinField}", '=', "{$currentTable}.id");
            } else {
                $query->leftJoin($joinTable, "{$joinTable}.id", '=', "{$currentTable}.{$joinField}");
            }

            $currentTable = $joinTable;
        }

        $query->addSelect(app($this->model)->getTable().'.*');

        return $query->orderBy($currentTable.'.'.end($parts), $sortBy);
    }

    private function safeSortDirection(mixed $direction): ?string
    {
        $direction = strtolower((string) $direction);

        return in_array($direction, ['asc', 'desc'], true) ? $direction : null;
    }

    private function safeOrderColumn(mixed $column): string
    {
        $column = (string) $column;

        return $this->isSafeColumnPath($column) ? $column : '';
    }

    private function isSafeColumnPath(mixed $column): bool
    {
        return is_string($column)
            && preg_match('/^[A-Za-z_][A-Za-z0-9_]*(\.[A-Za-z_][A-Za-z0-9_]*)*$/', $column) === 1;
    }

    private function isSafeRelationPath(string $path): bool
    {
        return $this->relatedModel($path) !== null;
    }

    private function isSafeFilterPath(mixed $path): bool
    {
        if (! $this->isSafeColumnPath($path)) {
            return false;
        }

        $parts = explode('.', $path);
        $field = array_pop($parts);
        $model = $parts === [] ? app($this->model) : $this->relatedModel(implode('.', $parts));

        if ($model === null) {
            return false;
        }

        return in_array($field, [
            $model->getKeyName(),
            ...$model->getFillable(),
            'created_at',
            'updated_at',
            'deleted_at',
        ], true);
    }

    private function relatedModel(string $path): ?Model
    {
        $model = app($this->model);

        try {
            foreach (explode('.', $path) as $relationName) {
                if (! method_exists($model, $relationName)) {
                    return null;
                }

                $relation = $model->{$relationName}();

                if (! $relation instanceof Relation) {
                    return null;
                }

                $model = $relation->getRelated();
            }
        } catch (Throwable) {
            return null;
        }

        return $model;
    }

    public function __prepareDataStore($data)
    {
        return $data;
    }

    public function __prepareQueryRelationShow()
    {
        $query = $this->query;

        foreach ($this->relationShow as $value) {
            if (isset($value)) {
                $query->with($value);
            }
        }

        return $query;
    }

    public function __prepareLoadRelation($row)
    {
        if (! $this->lockRelationParam) {
            $relations = request('relations', '');
            if (! empty($relations)) {
                $exp = explode(',', $relations);
                $rel = [];
                foreach ($exp as $relation) {
                    if (! empty(trim($relation))) {
                        $rel[] = trim($relation);
                    }
                }
                if (! empty($rel)) {
                    $row->load($rel);
                }
            }
        }

        return $row;
    }

    public function __prepareQueryUnLockRelations()
    {
        $query = $this->query;

        if (! $this->lockRelationParam) {
            $relations = request('relations', '');
            if (! empty($relations)) {
                $exp = explode(',', $relations);
                foreach ($exp as $relation) {
                    $query->with(trim($relation));
                }
            }
        }

        return $query;
    }

    public function __prepareDataUpdate($data)
    {
        return $data;
    }

    public function __prepareQueryRowShow()
    {
        return $this->query;
    }

    public function __prepareQueryRowUpdate()
    {
        return $this->query;
    }

    public function __prepareQueryRowDestroy()
    {
        return $this->query;
    }

    public function __prepareQueryBulkDestroy()
    {
        return $this->query;
    }
}
