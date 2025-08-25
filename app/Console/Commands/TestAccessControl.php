<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Models\User;
use App\Models\Department;
use App\Services\MessengerService;

class TestAccessControl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:access-control {phone}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test access control system with a messenger chat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phone = $this->argument('phone');
        
        $this->info('🔐 TESTING ACCESS CONTROL SYSTEM');
        $this->newLine();

        // Создаем тестовый мессенджер чат
        $this->info('📱 Creating test messenger chat...');
        
        $messengerService = app(MessengerService::class);
        $messengerService->handleIncomingMessage($phone, 'Тестовое сообщение для проверки доступа');
        
        $chat = Chat::messenger()->where('messenger_phone', $phone)->first();
        
        if (!$chat) {
            $this->error('Failed to create messenger chat!');
            return 1;
        }
        
        $this->info("✅ Chat created: ID {$chat->id}, Status: {$chat->messenger_status}");
        $this->newLine();

        // Получаем пользователей для тестирования
        $admin = User::where('role', 'admin')->first();
        $itManager = User::where('email', 'it.manager@test.com')->first();
        $hrManager = User::where('email', 'hr.manager@test.com')->first();
        
        if (!$itManager || !$hrManager) {
            $this->error('Test managers not found! Run: php artisan user:create-manager');
            return 1;
        }

        // Тестируем доступ для разных пользователей
        $this->info('👥 Testing access for different users:');
        $this->newLine();

        // Администратор (если есть)
        if ($admin) {
            $canView = $chat->canBeViewedBy($admin);
            $this->line("👑 Admin ({$admin->email}): " . ($canView ? '✅ CAN VIEW' : '❌ CANNOT VIEW'));
        }

        // IT менеджер
        $canView = $chat->canBeViewedBy($itManager);
        $this->line("💻 IT Manager ({$itManager->email}): " . ($canView ? '✅ CAN VIEW' : '❌ CANNOT VIEW'));

        // HR менеджер  
        $canView = $chat->canBeViewedBy($hrManager);
        $this->line("👔 HR Manager ({$hrManager->email}): " . ($canView ? '✅ CAN VIEW' : '❌ CANNOT VIEW'));

        $this->newLine();

        // Тестируем назначение отдела
        $this->info('🏢 Testing department assignment...');
        
        // Назначаем чат IT отделу
        $chat->update([
            'department_id' => 1, // IT отдел
            'messenger_status' => 'department_selected'
        ]);
        
        $this->info("✅ Chat assigned to IT department");
        
        // Проверяем доступ после назначения отдела
        $this->info('🔍 Access after department assignment:');
        
        $canView = $chat->canBeViewedBy($itManager);
        $this->line("💻 IT Manager: " . ($canView ? '✅ CAN VIEW' : '❌ CANNOT VIEW'));

        $canView = $chat->canBeViewedBy($hrManager);
        $this->line("👔 HR Manager: " . ($canView ? '✅ CAN VIEW' : '❌ CANNOT VIEW'));

        $this->newLine();

        // Тестируем автоматическое назначение при сообщении
        $this->info('💬 Testing auto-assignment on message...');
        
        // Симулируем сообщение от IT менеджера
        $chat->assignToUser($itManager);
        
        $this->info("✅ Chat auto-assigned to IT Manager");
        
        // Проверяем доступ после назначения
        $this->info('🔍 Access after user assignment:');
        
        $canView = $chat->canBeViewedBy($itManager);
        $this->line("💻 IT Manager: " . ($canView ? '✅ CAN VIEW' : '❌ CANNOT VIEW'));

        $canView = $chat->canBeViewedBy($hrManager);
        $this->line("👔 HR Manager: " . ($canView ? '✅ CAN VIEW' : '❌ CANNOT VIEW'));

        $this->newLine();
        $this->info('🎯 Test completed! Check the results above.');

        return 0;
    }
}
