<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Client;

class TestChatSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:chat-system';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the public chat system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Public Chat System...');
        $this->newLine();

        // Check if we have departments
        $this->info('📋 Checking departments...');
        $departments = \App\Models\Department::all();
        if ($departments->count() > 0) {
            $this->info('✅ Departments found: ' . $departments->count());
            foreach ($departments as $dept) {
                $this->line("  • {$dept->name} (ID: {$dept->id})");
            }
        } else {
            $this->warn('⚠️  No departments found. Creating test departments...');
            $this->createTestDepartments();
        }

        $this->newLine();

        // Check existing chats
        $this->info('💬 Checking existing chats...');
        $chats = Chat::where('is_messenger_chat', true)->get();
        $this->info('Found ' . $chats->count() . ' messenger chats');

        if ($chats->count() > 0) {
            $this->table(
                ['ID', 'Title', 'Status', 'Phone', 'Messages', 'Created'],
                $chats->map(function($chat) {
                    return [
                        $chat->id,
                        $chat->title,
                        $chat->status,
                        $chat->messenger_phone,
                        $chat->messages->count(),
                        $chat->created_at->format('d.m.Y H:i')
                    ];
                })->toArray()
            );
        }

        $this->newLine();

        // Show URLs
        $this->info('🔗 Chat URLs:');
        $this->line('  • Main chat: http://127.0.0.1:8000/chat');
        
        if ($chats->count() > 0) {
            $firstChat = $chats->first();
            $this->line("  • Specific chat: http://127.0.0.1:8000/chat/{$firstChat->id}");
        }

        $this->newLine();

        // Test API endpoints
        $this->info('🔧 API Endpoints:');
        $this->line('  • POST /chat/start - Start new chat');
        $this->line('  • POST /chat/{id}/send - Send message');
        $this->line('  • GET /chat/{id}/messages - Get messages');
        $this->line('  • POST /chat/{id}/department - Select department');

        $this->newLine();

        // Show sample data
        $this->info('📝 Sample data for testing:');
        $this->line('Start chat:');
        $this->line('  POST /chat/start');
        $this->line('  {');
        $this->line('    "name": "Иван Иванов",');
        $this->line('    "phone": "+7 999 123-45-67",');
        $this->line('    "message": "Здравствуйте, у меня есть вопрос"');
        $this->line('  }');

        $this->newLine();

        $this->line('Select department:');
        $this->line('  POST /chat/{id}/department');
        $this->line('  {');
        $this->line('    "department": "hr"  // or "it" or "exit"');
        $this->line('  }');

        $this->newLine();

        $this->line('Send message:');
        $this->line('  POST /chat/{id}/send');
        $this->line('  {');
        $this->line('    "content": "Мое сообщение",');
        $this->line('    "client_name": "Иван Иванов"');
        $this->line('  }');

        return 0;
    }

    /**
     * Create test departments if none exist
     */
    private function createTestDepartments()
    {
        $departments = [
            ['name' => 'IT отдел', 'description' => 'Техническая поддержка'],
            ['name' => 'HR отдел', 'description' => 'Кадровые вопросы'],
        ];

        foreach ($departments as $dept) {
            \App\Models\Department::create($dept);
        }

        $this->info('✅ Test departments created');
    }
}
