<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class CreateTestManager extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-manager {email} {department_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test manager user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $departmentId = $this->argument('department_id');

        // Проверяем существование отдела
        $department = Department::find($departmentId);
        if (!$department) {
            $this->error("Department with ID {$departmentId} not found!");
            $this->info('Available departments:');
            Department::all()->each(function($dept) {
                $this->line("  - ID:{$dept->id} {$dept->name}");
            });
            return 1;
        }

        // Проверяем, не существует ли уже пользователь
        if (User::where('email', $email)->exists()) {
            $this->error("User with email {$email} already exists!");
            return 1;
        }

        // Создаем пользователя
        $user = User::create([
            'name' => 'Test Manager',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'manager',
            'department_id' => $departmentId,
            'email_verified_at' => now(),
        ]);

        $this->info("✅ Manager created successfully!");
        $this->info("Email: {$email}");
        $this->info("Password: password");
        $this->info("Department: {$department->name}");
        $this->info("Role: manager");
        $this->newLine();
        
        $this->info("🔗 Login URL: " . url('/login'));
        $this->info("📱 Messenger URL: " . url('/admin/messenger'));

        return 0;
    }
}
