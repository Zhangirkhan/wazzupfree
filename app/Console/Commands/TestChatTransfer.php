<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MessengerService;
use App\Models\Chat;
use App\Models\Department;
use App\Models\User;

class TestChatTransfer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:test-transfer {chat_id} {--type=department} {--target=} {--reason=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Тестирует систему передачи чатов между отделами и менеджерами';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chatId = $this->argument('chat_id');
        $type = $this->option('type'); // department или user
        $target = $this->option('target');
        $reason = $this->option('reason') ?? 'Тестовая передача';

        $this->info("Тестирование передачи чата ID: {$chatId}");
        $this->info("Тип передачи: {$type}");
        $this->info("Цель: {$target}");
        $this->info("Причина: {$reason}");

        // Создаем экземпляр сервиса
        $messengerService = app(MessengerService::class);

        try {
            // Находим чат
            $chat = Chat::with(['department', 'assignedTo'])->find($chatId);
            if (!$chat) {
                $this->error("❌ Чат с ID {$chatId} не найден");
                return 1;
            }

            $this->info("✅ Чат найден:");
            $this->info("   Название: {$chat->title}");
            $this->info("   Текущий отдел: " . ($chat->department ? $chat->department->name : 'Не назначен'));
            $this->info("   Текущий менеджер: " . ($chat->assignedTo ? $chat->assignedTo->name : 'Не назначен'));
            $this->info("   Статус: {$chat->messenger_status}");

            // Показываем доступные отделы и менеджеры
            $this->info("\n📋 Доступные отделы:");
            $departments = $messengerService->getAvailableDepartments($chat->department_id);
            foreach ($departments as $dept) {
                $this->info("   ID: {$dept->id} - {$dept->name}");
            }

            $this->info("\n👥 Доступные менеджеры:");
            $managers = $messengerService->getAvailableManagers($chat->assigned_to, $chat->department_id);
            foreach ($managers as $manager) {
                $deptName = $manager->department ? $manager->department->name : 'Без отдела';
                $this->info("   ID: {$manager->id} - {$manager->name} ({$deptName})");
            }

            // Выполняем передачу
            if ($type === 'department' && $target) {
                $this->info("\n🔄 Передача в отдел ID: {$target}");
                $result = $messengerService->transferToDepartmentWithNotification($chat, $target, $reason);
                
                if ($result) {
                    $newDepartment = Department::find($target);
                    $this->info("✅ Чат успешно передан в отдел: {$newDepartment->name}");
                } else {
                    $this->error("❌ Ошибка при передаче в отдел");
                    return 1;
                }
            } elseif ($type === 'user' && $target) {
                $this->info("\n🔄 Передача менеджеру ID: {$target}");
                $result = $messengerService->transferToUserWithNotification($chat, $target, $reason);
                
                if ($result) {
                    $newUser = User::find($target);
                    $this->info("✅ Чат успешно передан менеджеру: {$newUser->name}");
                } else {
                    $this->error("❌ Ошибка при передаче менеджеру");
                    return 1;
                }
            } else {
                $this->error("❌ Неверные параметры. Используйте --type=department|user и --target=ID");
                return 1;
            }

            // Показываем обновленную информацию о чате
            $chat->refresh();
            $this->info("\n📊 Обновленная информация о чате:");
            $this->info("   Отдел: " . ($chat->department ? $chat->department->name : 'Не назначен'));
            $this->info("   Менеджер: " . ($chat->assignedTo ? $chat->assignedTo->name : 'Не назначен'));

            // Показываем историю передач
            $this->info("\n📜 История передач:");
            $history = $messengerService->getChatTransferHistory($chat);
            if ($history->count() > 0) {
                foreach ($history as $transfer) {
                    $this->info("   [{$transfer->created_at->format('H:i:s')}] {$transfer->content}");
                    if (isset($transfer->metadata['transfer_reason'])) {
                        $this->info("       Причина: {$transfer->metadata['transfer_reason']}");
                    }
                }
            } else {
                $this->info("   История передач пуста");
            }

            $this->info("\n✅ Тест передачи завершен успешно!");

        } catch (\Exception $e) {
            $this->error("❌ Ошибка: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
