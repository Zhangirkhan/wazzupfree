<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Models\Department;
use App\Models\User;

class SystemStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show current system status for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 SYSTEM STATUS FOR TESTING');
        $this->newLine();

        // Wazzup24 настройки
        $this->info('📡 Wazzup24 Configuration:');
        $this->line('API Key: ' . (config('wazzup24.api.key') ? '✅ Set' : '❌ Not set'));
        $this->line('Channel ID: ' . (config('wazzup24.api.channel_id') ? '✅ Set' : '❌ Not set'));
        $this->line('Webhook URL: ' . config('wazzup24.api.webhook_url'));
        $this->newLine();

        // Отделы
        $departments = Department::all();
        $this->info('🏢 Departments (' . $departments->count() . '):');
        foreach ($departments as $dept) {
            $this->line("  - ID:{$dept->id} {$dept->name}");
        }
        $this->newLine();

        // Пользователи по ролям
        $adminCount = User::where('role', 'admin')->count();
        $managerCount = User::where('role', 'manager')->count();
        $employeeCount = User::where('role', 'employee')->count();
        
        $this->info('👥 Users by Role:');
        $this->line("  - Admins: {$adminCount}");
        $this->line("  - Managers: {$managerCount}");
        $this->line("  - Employees: {$employeeCount}");
        $this->newLine();

        // Мессенджер чаты
        $messengerChats = Chat::messenger()->get();
        $this->info('💬 Messenger Chats (' . $messengerChats->count() . '):');
        
        if ($messengerChats->count() > 0) {
            foreach ($messengerChats as $chat) {
                $this->line("  - ID:{$chat->id} Phone:{$chat->messenger_phone} Status:{$chat->messenger_status}");
            }
        } else {
            $this->line('  - No messenger chats found');
        }
        $this->newLine();

        // Статистика мессенджера
        $stats = [
            'menu' => Chat::messenger()->where('messenger_status', 'menu')->count(),
            'department_selected' => Chat::messenger()->where('messenger_status', 'department_selected')->count(),
            'active' => Chat::messenger()->where('messenger_status', 'active')->count(),
            'completed' => Chat::messenger()->where('messenger_status', 'completed')->count(),
        ];

        $this->info('📊 Messenger Stats:');
        $this->line("  - In Menu: {$stats['menu']}");
        $this->line("  - Department Selected: {$stats['department_selected']}");
        $this->line("  - Active: {$stats['active']}");
        $this->line("  - Completed: {$stats['completed']}");
        $this->newLine();

        // Полезные команды
        $this->info('🚀 Testing Commands:');
        $this->line('  php artisan wazzup:test                    - Test API connection');
        $this->line('  php artisan webhook:monitor                - Monitor webhooks live');
        $this->line('  php artisan messenger:test 77012345678     - Create test messenger chat');
        $this->line('  php artisan user:assign-role user@test.com manager - Assign user role');
        $this->newLine();

        $this->info('📱 To test messaging:');
        $this->line('  1. Send WhatsApp message to: 77760664069');
        $this->line('  2. Watch logs with: php artisan webhook:monitor');
        $this->line('  3. Check admin panel: /admin/messenger');

        return 0;
    }
}
