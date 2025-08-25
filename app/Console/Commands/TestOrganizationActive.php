<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Organization;
use App\Models\Department;

class TestOrganizationActive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:organization-active';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Organization::active() method';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Organization::active() method...');
        
        try {
            $organizations = Organization::active()->get();
            $this->info('✅ Organization::active() works! Found ' . $organizations->count() . ' organizations');
            
            foreach ($organizations as $org) {
                $this->line("  • {$org->name} (ID: {$org->id})");
            }
        } catch (\Exception $e) {
            $this->error('❌ Organization::active() failed: ' . $e->getMessage());
            $this->line('Stack trace:');
            $this->line($e->getTraceAsString());
        }

        $this->newLine();
        $this->info('🧪 Testing Department::active() method...');
        
        try {
            $departments = Department::active()->get();
            $this->info('✅ Department::active() works! Found ' . $departments->count() . ' departments');
            
            foreach ($departments as $dept) {
                $this->line("  • {$dept->name} (ID: {$dept->id})");
            }
        } catch (\Exception $e) {
            $this->error('❌ Department::active() failed: ' . $e->getMessage());
            $this->line('Stack trace:');
            $this->line($e->getTraceAsString());
        }

        return 0;
    }
}
