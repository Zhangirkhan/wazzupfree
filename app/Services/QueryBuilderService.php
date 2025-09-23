<?php

namespace App\Services;

use App\Contracts\QueryBuilderInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QueryBuilderService implements QueryBuilderInterface
{
    private Builder $query;
    private array $eagerLoads = [];
    private array $queryLog = [];

    public function __construct(Model $model)
    {
        $this->query = $model->newQuery();
        $this->enableQueryLogging();
    }

    public function with(array $relations): self
    {
        $this->eagerLoads = array_merge($this->eagerLoads, $relations);
        $this->query->with($relations);
        return $this;
    }

    public function where(string $column, $operator = null, $value = null): self
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->query->where($column, $operator, $value);
        return $this;
    }

    public function orWhere(string $column, $operator = null, $value = null): self
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->query->orWhere($column, $operator, $value);
        return $this;
    }

    public function whereIn(string $column, array $values): self
    {
        $this->query->whereIn($column, $values);
        return $this;
    }

    public function whereHas(string $relation, callable $callback = null): self
    {
        $this->query->whereHas($relation, $callback);
        return $this;
    }

    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $this->query->orderBy($column, $direction);
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->query->limit($limit);
        return $this;
    }

    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        $this->logQuery('paginate');
        return $this->query->paginate($perPage);
    }

    public function get()
    {
        $this->logQuery('get');
        return $this->query->get();
    }

    public function first()
    {
        $this->logQuery('first');
        return $this->query->first();
    }

    public function count(): int
    {
        $this->logQuery('count');
        return $this->query->count();
    }

    public function exists(): bool
    {
        $this->logQuery('exists');
        return $this->query->exists();
    }

    public function toSql(): string
    {
        return $this->query->toSql();
    }

    private function enableQueryLogging(): void
    {
        if (config('app.debug')) {
            DB::enableQueryLog();
        }
    }

    private function logQuery(string $method): void
    {
        if (config('app.debug')) {
            $queries = DB::getQueryLog();
            $lastQuery = end($queries);
            
            if ($lastQuery) {
                $this->queryLog[] = [
                    'method' => $method,
                    'sql' => $lastQuery['query'],
                    'bindings' => $lastQuery['bindings'],
                    'time' => $lastQuery['time'],
                    'eager_loads' => $this->eagerLoads
                ];

                Log::debug('Database Query Executed', [
                    'method' => $method,
                    'sql' => $lastQuery['query'],
                    'bindings' => $lastQuery['bindings'],
                    'time' => $lastQuery['time'] . 'ms',
                    'eager_loads' => $this->eagerLoads
                ]);
            }
        }
    }

    public function getQueryLog(): array
    {
        return $this->queryLog;
    }
}
