<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Services\MessengerService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoCloseChats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chats:auto-close {--dry-run : Показать какие чаты будут закрыты без фактического закрытия}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Автоматическое закрытие активных чатов каждые 8 часов для подсчета переговоров';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🕐 Начинаем автоматическое закрытие чатов (каждые 8 часов)');
        
        // Находим активные чаты старше 8 часов
        $cutoffTime = Carbon::now()->subHours(8);
        
        $chatsToClose = Chat::where('messenger_status', 'active')
            ->where('updated_at', '<', $cutoffTime)
            ->where('is_messenger_chat', true)
            ->with(['assignedTo', 'department'])
            ->get();

        $this->info("Найдено чатов для закрытия: {$chatsToClose->count()}");

        if ($chatsToClose->count() === 0) {
            $this->info('✅ Нет чатов для закрытия');
            return 0;
        }

        $messengerService = app(MessengerService::class);
        $closedCount = 0;
        $errorCount = 0;

        foreach ($chatsToClose as $chat) {
            $this->line("Обрабатываем чат {$chat->id}: {$chat->title}");
            $this->line("  Последняя активность: {$chat->updated_at}");
            $this->line("  Назначен: " . ($chat->assignedTo ? $chat->assignedTo->name : 'не назначен'));
            $this->line("  Отдел: " . ($chat->department ? $chat->department->name : 'не выбран'));

            if ($this->option('dry-run')) {
                $this->line("  [DRY RUN] Будет закрыт");
                continue;
            }

            try {
                // Закрываем чат через MessengerService
                $result = $messengerService->closeChat(
                    $chat->id, 
                    1, // Системный пользователь
                    'Автоматическое закрытие чата (8 часов неактивности)'
                );

                if ($result['success']) {
                    $this->line("  ✅ Закрыт успешно");
                    $closedCount++;
                    
                    // Логируем для статистики
                    Log::info('Chat auto-closed for statistics', [
                        'chat_id' => $chat->id,
                        'manager_id' => $chat->assigned_to,
                        'department_id' => $chat->department_id,
                        'duration_hours' => $chat->created_at->diffInHours($chat->updated_at),
                        'messages_count' => $chat->messages()->count()
                    ]);
                } else {
                    $this->error("  ❌ Ошибка: {$result['error']}");
                    $errorCount++;
                }
            } catch (\Exception $e) {
                $this->error("  ❌ Исключение: {$e->getMessage()}");
                $errorCount++;
            }
        }

        if (!$this->option('dry-run')) {
            $this->info("✅ Автоматическое закрытие завершено");
            $this->info("Закрыто чатов: {$closedCount}");
            $this->info("Ошибок: {$errorCount}");
            
            // Логируем общую статистику
            Log::info('Auto-close chats completed', [
                'total_processed' => $chatsToClose->count(),
                'closed_successfully' => $closedCount,
                'errors' => $errorCount
            ]);
        } else {
            $this->info("📋 DRY RUN завершен. Найдено {$chatsToClose->count()} чатов для закрытия");
        }

        return 0;
    }
}