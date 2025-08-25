<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:login {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Quick admin login and dashboard access';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'admin@testcompany.com';

        $this->info('🔐 Admin Login Helper');
        $this->newLine();

        // Проверяем пользователя
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error('❌ User not found: ' . $email);
            $this->info('💡 Create admin user: php artisan user:create-admin ' . $email);
            return 1;
        }

        $this->info('✅ User found:');
        $this->line('  Name: ' . $user->name);
        $this->line('  Email: ' . $user->email);
        $this->line('  Role: ' . $user->role);
        $this->newLine();

        // Проверяем права доступа
        if ($user->role !== 'admin') {
            $this->warn('⚠️  User is not admin! Role: ' . $user->role);
            $this->info('💡 Update to admin: php artisan user:create-admin ' . $email);
        }

        $this->info('🌐 Access URLs:');
        $this->line('  Login page: http://127.0.0.1:8000/login');
        $this->line('  Dashboard: http://127.0.0.1:8000/admin/dashboard');
        $this->newLine();

        $this->info('📋 Login Credentials:');
        $this->line('  Email: ' . $email);
        $this->line('  Password: admin123');
        $this->newLine();

        $this->info('🚀 Quick Actions:');
        $this->line('  1. Open: http://127.0.0.1:8000/login');
        $this->line('  2. Enter email: ' . $email);
        $this->line('  3. Enter password: admin123');
        $this->line('  4. Click "Войти в админ-панель"');
        $this->newLine();

        $this->info('📱 Available sections:');
        $this->line('  • Dashboard: http://127.0.0.1:8000/admin/dashboard');
        $this->line('  • Messenger: http://127.0.0.1:8000/admin/messenger');
        $this->line('  • Users: http://127.0.0.1:8000/admin/users');
        $this->line('  • Clients: http://127.0.0.1:8000/admin/clients');
        $this->line('  • Settings: http://127.0.0.1:8000/admin/settings');

        return 0;
    }
}
