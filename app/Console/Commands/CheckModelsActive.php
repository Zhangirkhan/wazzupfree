<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class CheckModelsActive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'models:check-active';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check models for active() method and is_active field';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking models for active() method and is_active field...');
        $this->newLine();

        $modelsPath = app_path('Models');
        $modelFiles = File::glob($modelsPath . '/*.php');

        $results = [];

        foreach ($modelFiles as $modelFile) {
            $modelName = basename($modelFile, '.php');
            $className = "App\\Models\\{$modelName}";
            
            try {
                $reflection = new ReflectionClass($className);
                $model = new $className();
                
                $hasIsActiveField = $this->hasIsActiveField($model);
                $hasActiveMethod = $this->hasActiveMethod($reflection);
                $hasInactiveMethod = $this->hasInactiveMethod($reflection);
                
                $results[] = [
                    'model' => $modelName,
                    'has_is_active' => $hasIsActiveField ? '✅' : '❌',
                    'has_active_method' => $hasActiveMethod ? '✅' : '❌',
                    'has_inactive_method' => $hasInactiveMethod ? '✅' : '❌',
                    'status' => $this->getStatus($hasIsActiveField, $hasActiveMethod, $hasInactiveMethod)
                ];
                
            } catch (\Exception $e) {
                $results[] = [
                    'model' => $modelName,
                    'has_is_active' => '❌',
                    'has_active_method' => '❌',
                    'has_inactive_method' => '❌',
                    'status' => '🔴 Error: ' . $e->getMessage()
                ];
            }
        }

        $this->table(
            ['Model', 'is_active field', 'active() method', 'inactive() method', 'Status'],
            $results
        );

        $this->newLine();
        $this->info('📋 Summary:');
        
        $modelsWithIsActive = array_filter($results, fn($r) => str_contains($r['has_is_active'], '✅'));
        $modelsWithActiveMethod = array_filter($results, fn($r) => str_contains($r['has_active_method'], '✅'));
        $modelsWithInactiveMethod = array_filter($results, fn($r) => str_contains($r['has_inactive_method'], '✅'));
        
        $this->line("• Models with is_active field: " . count($modelsWithIsActive));
        $this->line("• Models with active() method: " . count($modelsWithActiveMethod));
        $this->line("• Models with inactive() method: " . count($modelsWithInactiveMethod));
        
        $this->newLine();
        
        // Show models that need active() method
        $modelsNeedingActive = array_filter($results, function($r) {
            return str_contains($r['has_is_active'], '✅') && 
                   str_contains($r['has_active_method'], '❌');
        });
        
        if (!empty($modelsNeedingActive)) {
            $this->warn('⚠️  Models that need active() method:');
            foreach ($modelsNeedingActive as $model) {
                $this->line("  • {$model['model']}");
            }
        } else {
            $this->info('✅ All models with is_active field have active() method!');
        }

        return 0;
    }

    /**
     * Check if model has is_active field
     */
    private function hasIsActiveField($model): bool
    {
        $fillable = $model->getFillable();
        return in_array('is_active', $fillable);
    }

    /**
     * Check if model has active() method
     */
    private function hasActiveMethod(ReflectionClass $reflection): bool
    {
        return $reflection->hasMethod('scopeActive');
    }

    /**
     * Check if model has inactive() method
     */
    private function hasInactiveMethod(ReflectionClass $reflection): bool
    {
        return $reflection->hasMethod('scopeInactive');
    }

    /**
     * Get status string
     */
    private function getStatus(bool $hasIsActive, bool $hasActive, bool $hasInactive): string
    {
        if (!$hasIsActive) {
            return '⚪ No is_active field';
        }
        
        if (!$hasActive) {
            return '🔴 Missing active() method';
        }
        
        if (!$hasInactive) {
            return '🟡 Missing inactive() method';
        }
        
        return '✅ Complete';
    }
}
