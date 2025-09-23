<?php

namespace App\Contracts;

interface UnitOfWorkInterface
{
    public function begin(): void;
    public function commit(): void;
    public function rollback(): void;
    public function isActive(): bool;
    public function execute(callable $callback);
    public function addOperation(callable $operation): void;
    public function addRollback(callable $rollback): void;
}
