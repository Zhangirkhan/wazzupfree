<?php

namespace App\Services;

use App\Contracts\UnitOfWorkInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class UnitOfWorkService implements UnitOfWorkInterface
{
    private bool $isActive = false;
    private array $operations = [];
    private array $rollbacks = [];

    public function begin(): void
    {
        if ($this->isActive) {
            throw new Exception('Transaction is already active');
        }

        DB::beginTransaction();
        $this->isActive = true;
        $this->operations = [];
        $this->rollbacks = [];

        Log::debug('Unit of Work transaction started');
    }

    public function commit(): void
    {
        if (!$this->isActive) {
            throw new Exception('No active transaction to commit');
        }

        try {
            // Выполняем все операции
            foreach ($this->operations as $operation) {
                $operation();
            }

            DB::commit();
            $this->isActive = false;
            $this->operations = [];
            $this->rollbacks = [];

            Log::debug('Unit of Work transaction committed');
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    public function rollback(): void
    {
        if (!$this->isActive) {
            return;
        }

        try {
            // Выполняем rollback операции в обратном порядке
            foreach (array_reverse($this->rollbacks) as $rollback) {
                $rollback();
            }
        } catch (Exception $e) {
            Log::error('Error during rollback operations', [
                'error' => $e->getMessage()
            ]);
        } finally {
            DB::rollback();
            $this->isActive = false;
            $this->operations = [];
            $this->rollbacks = [];

            Log::debug('Unit of Work transaction rolled back');
        }
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function execute(callable $callback)
    {
        $this->begin();
        
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    public function addOperation(callable $operation): void
    {
        if (!$this->isActive) {
            throw new Exception('No active transaction to add operation');
        }

        $this->operations[] = $operation;
    }

    public function addRollback(callable $rollback): void
    {
        if (!$this->isActive) {
            throw new Exception('No active transaction to add rollback');
        }

        $this->rollbacks[] = $rollback;
    }
}
