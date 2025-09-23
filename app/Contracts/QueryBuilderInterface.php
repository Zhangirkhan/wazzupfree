<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface QueryBuilderInterface
{
    public function with(array $relations): self;
    public function where(string $column, $operator = null, $value = null): self;
    public function orWhere(string $column, $operator = null, $value = null): self;
    public function whereIn(string $column, array $values): self;
    public function whereHas(string $relation, callable $callback = null): self;
    public function orderBy(string $column, string $direction = 'asc'): self;
    public function limit(int $limit): self;
    public function paginate(int $perPage = 20): LengthAwarePaginator;
    public function get();
    public function first();
    public function count(): int;
    public function exists(): bool;
    public function toSql(): string;
}
