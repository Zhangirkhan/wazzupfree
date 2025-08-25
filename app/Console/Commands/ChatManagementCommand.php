<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Organization;
use App\Models\Chat;
use App\Models\Message;

class ChatManagementCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:manage {action} {--user=} {--chat=} {--organization=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage chats and users in the corporate chat system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'list-users':
                $this->listUsers();
                break;
            case 'list-chats':
                $this->listChats();
                break;
            case 'list-organizations':
                $this->listOrganizations();
                break;
            case 'close-chat':
                $this->closeChat();
                break;
            case 'stats':
                $this->showStats();
                break;
            default:
                $this->error("Unknown action: {$action}");
                $this->showHelp();
        }
    }

    private function listUsers()
    {
        $users = User::with(['organizations', 'departments', 'roles'])->get();

        $this->info('Users in the system:');
        $this->table(
            ['ID', 'Name', 'Email', 'Position', 'Organizations'],
            $users->map(function ($user) {
                $orgs = $user->organizations->pluck('name')->implode(', ');
                return [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->position ?? 'N/A',
                    $orgs ?: 'None'
                ];
            })
        );
    }

    private function listChats()
    {
        $organizationId = $this->option('organization');
        
        $query = Chat::with(['organization', 'creator', 'assignedTo', 'participants.user']);
        
        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }
        
        $chats = $query->get();

        $this->info('Chats in the system:');
        $this->table(
            ['ID', 'Title', 'Type', 'Status', 'Organization', 'Creator', 'Participants'],
            $chats->map(function ($chat) {
                $participants = $chat->participants->pluck('user.name')->implode(', ');
                return [
                    $chat->id,
                    $chat->title ?? 'Untitled',
                    $chat->type,
                    $chat->status,
                    $chat->organization->name,
                    $chat->creator->name,
                    $participants ?: 'None'
                ];
            })
        );
    }

    private function listOrganizations()
    {
        $organizations = Organization::with(['departments', 'roles', 'users'])->get();

        $this->info('Organizations in the system:');
        $this->table(
            ['ID', 'Name', 'Slug', 'Departments', 'Roles', 'Users'],
            $organizations->map(function ($org) {
                return [
                    $org->id,
                    $org->name,
                    $org->slug,
                    $org->departments->count(),
                    $org->roles->count(),
                    $org->users->count()
                ];
            })
        );
    }

    private function closeChat()
    {
        $chatId = $this->option('chat');
        
        if (!$chatId) {
            $this->error('Please specify chat ID with --chat option');
            return;
        }

        $chat = Chat::find($chatId);
        
        if (!$chat) {
            $this->error("Chat with ID {$chatId} not found");
            return;
        }

        if ($chat->status === 'closed') {
            $this->warn("Chat {$chatId} is already closed");
            return;
        }

        $chat->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        // Send system message
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => 1, // System user
            'content' => 'Беседа завершена администратором',
            'type' => 'system',
        ]);

        $this->info("Chat {$chatId} has been closed successfully");
    }

    private function showStats()
    {
        $this->info('System Statistics:');
        
        $stats = [
            ['Metric', 'Count'],
            ['Users', User::count()],
            ['Organizations', Organization::count()],
            ['Active Chats', Chat::where('status', 'active')->count()],
            ['Closed Chats', Chat::where('status', 'closed')->count()],
            ['Total Messages', Message::count()],
            ['System Messages', Message::where('type', 'system')->count()],
            ['Hidden Messages', Message::where('is_hidden', true)->count()],
        ];

        $this->table(['Metric', 'Count'], array_slice($stats, 1));
    }

    private function showHelp()
    {
        $this->info('Available actions:');
        $this->line('  list-users              - List all users');
        $this->line('  list-chats              - List all chats');
        $this->line('  list-organizations      - List all organizations');
        $this->line('  close-chat --chat=ID    - Close a specific chat');
        $this->line('  stats                   - Show system statistics');
        
        $this->info('Examples:');
        $this->line('  php artisan chat:manage list-users');
        $this->line('  php artisan chat:manage list-chats --organization=1');
        $this->line('  php artisan chat:manage close-chat --chat=5');
        $this->line('  php artisan chat:manage stats');
    }
}
