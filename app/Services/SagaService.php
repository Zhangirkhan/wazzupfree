<?php

namespace App\Services;

use App\Contracts\SagaInterface;
use Illuminate\Support\Facades\Log;
use Exception;

class SagaService implements SagaInterface
{
    private array $steps = [];
    private array $completedSteps = [];
    private ?string $currentStep = null;
    private bool $isCompleted = false;

    public function addStep(string $name, callable $action, callable $compensation): void
    {
        $this->steps[$name] = [
            'action' => $action,
            'compensation' => $compensation,
            'completed' => false
        ];
    }

    public function execute(): void
    {
        Log::info('Saga execution started', [
            'steps_count' => count($this->steps)
        ]);

        foreach ($this->steps as $stepName => $step) {
            $this->currentStep = $stepName;
            
            try {
                Log::debug("Executing saga step: {$stepName}");
                
                $result = $step['action']();
                $this->steps[$stepName]['completed'] = true;
                $this->completedSteps[] = $stepName;
                
                Log::debug("Saga step completed: {$stepName}");
            } catch (Exception $e) {
                Log::error("Saga step failed: {$stepName}", [
                    'error' => $e->getMessage()
                ]);
                
                $this->compensate();
                throw new Exception("Saga execution failed at step '{$stepName}': " . $e->getMessage());
            }
        }

        $this->isCompleted = true;
        $this->currentStep = null;
        
        Log::info('Saga execution completed successfully');
    }

    public function compensate(): void
    {
        Log::info('Starting saga compensation', [
            'completed_steps' => $this->completedSteps
        ]);

        // Выполняем компенсацию в обратном порядке
        foreach (array_reverse($this->completedSteps) as $stepName) {
            try {
                Log::debug("Compensating saga step: {$stepName}");
                
                $this->steps[$stepName]['compensation']();
                $this->steps[$stepName]['completed'] = false;
                
                Log::debug("Saga step compensated: {$stepName}");
            } catch (Exception $e) {
                Log::error("Saga compensation failed for step: {$stepName}", [
                    'error' => $e->getMessage()
                ]);
                // Продолжаем компенсацию других шагов
            }
        }

        $this->completedSteps = [];
        $this->isCompleted = false;
        
        Log::info('Saga compensation completed');
    }

    public function isCompleted(): bool
    {
        return $this->isCompleted;
    }

    public function getCurrentStep(): ?string
    {
        return $this->currentStep;
    }
}
