<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ClearAllCaches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear {--force : Force clear without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all Laravel caches and temporary files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Starting complete cache cleanup...');
        $this->newLine();

        // 1. Laravel caches
        $this->info('📦 Clearing Laravel caches...');
        $this->clearLaravelCaches();

        // 2. Application caches
        $this->info('🔧 Clearing application caches...');
        $this->clearApplicationCaches();

        // 3. Temporary files
        $this->info('🗂️  Clearing temporary files...');
        $this->clearTemporaryFiles();

        // 4. Log files (автоматически)
        $this->info('📄 Clearing log files...');
        $this->clearLogFiles();

        // 5. Composer cache (автоматически)
        $this->info('🎼 Clearing Composer cache...');
        $this->clearComposerCache();

        // // 6. Node modules cache (если существует)
        // if (File::exists(base_path('package.json'))) {
        //     $this->info('📦 Clearing Node.js cache...');
        //     $this->clearNodeCache();
        // }

        $this->newLine();
        $this->info('✅ Cache cleanup completed successfully!');
        $this->info('💡 You may need to restart your application server.');
        
        return 0;
    }

    /**
     * Clear Laravel caches
     */
    protected function clearLaravelCaches()
    {
        $caches = [
            'config:clear' => 'Configuration cache',
            'route:clear' => 'Route cache',
            'view:clear' => 'View cache',
            // 'cache:clear' => 'Application cache',
            'clear-compiled' => 'Compiled classes',
            // 'optimize:clear' => 'Optimization cache'
        ];

        foreach ($caches as $command => $description) {
            try {
                $this->line("  - {$description}...");
                Artisan::call($command);
                $this->info("    ✅ Cleared");
            } catch (\Exception $e) {
                $this->error("    ❌ Error: " . $e->getMessage());
            }
        }
    }

    /**
     * Clear application caches
     */
    protected function clearApplicationCaches()
    {
        $paths = [
            storage_path('framework/cache/data'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('framework/testing'),
            storage_path('app/public/cache'),
            public_path('cache')
        ];

        foreach ($paths as $path) {
            if (File::exists($path)) {
                try {
                    $this->line("  - " . basename($path) . "...");
                    File::deleteDirectory($path);
                    File::makeDirectory($path, 0755, true);
                    $this->info("    ✅ Cleared");
                } catch (\Exception $e) {
                    $this->error("    ❌ Error: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Clear temporary files
     */
    protected function clearTemporaryFiles()
    {
        $patterns = [
            storage_path('logs/*.log'),
            storage_path('framework/cache/*'),
            storage_path('framework/sessions/*'),
            storage_path('framework/views/*'),
            storage_path('app/public/temp/*'),
            public_path('temp/*')
        ];

        foreach ($patterns as $pattern) {
            $files = glob($pattern);
            foreach ($files as $file) {
                if (is_file($file)) {
                    try {
                        unlink($file);
                        $this->line("  - Deleted: " . basename($file));
                    } catch (\Exception $e) {
                        $this->error("  - Error deleting: " . basename($file));
                    }
                }
            }
        }
    }

    /**
     * Clear log files
     */
    protected function clearLogFiles()
    {
        $logPath = storage_path('logs');
        $files = glob($logPath . '/*.log');

        foreach ($files as $file) {
            try {
                file_put_contents($file, '');
                $this->line("  - Cleared: " . basename($file));
            } catch (\Exception $e) {
                $this->error("  - Error clearing: " . basename($file));
            }
        }
    }

    /**
     * Clear Composer cache
     */
    protected function clearComposerCache()
    {
        try {
            $output = shell_exec('composer clear-cache 2>&1');
            $this->line("  - Composer cache cleared");
        } catch (\Exception $e) {
            $this->error("  - Error clearing Composer cache: " . $e->getMessage());
        }
    }

    /**
     * Clear Node.js cache
     */
    protected function clearNodeCache()
    {
        $nodeCachePath = base_path('node_modules/.cache');
        
        if (File::exists($nodeCachePath)) {
            try {
                File::deleteDirectory($nodeCachePath);
                $this->line("  - Node.js cache cleared");
            } catch (\Exception $e) {
                $this->error("  - Error clearing Node.js cache: " . $e->getMessage());
            }
        } else {
            $this->line("  - No Node.js cache found");
        }
    }
}
