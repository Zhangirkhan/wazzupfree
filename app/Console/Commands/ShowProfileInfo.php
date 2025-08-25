<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ShowProfileInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profile:info {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show profile information for users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        if ($email) {
            $this->showUserProfile($email);
        } else {
            $this->showAllProfiles();
        }

        return 0;
    }

    /**
     * Показать профиль конкретного пользователя
     */
    protected function showUserProfile($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error('❌ Пользователь не найден: ' . $email);
            return;
        }

        $this->info('👤 Профиль пользователя: ' . $user->name);
        $this->newLine();

        $this->table(
            ['Поле', 'Значение'],
            [
                ['ID', $user->id],
                ['Имя', $user->name],
                ['Email', $user->email],
                ['Телефон', $user->phone ?: 'Не указан'],
                ['Должность', $user->position ?: 'Не указана'],
                ['Роль', $this->getRoleName($user->role)],
                ['Отдел', $user->department->name ?? 'Не назначен'],
                ['Дата регистрации', $user->created_at->format('d.m.Y H:i')],
                ['Аватар', $user->avatar ? 'Есть' : 'Нет'],
            ]
        );

        $this->newLine();
        $this->info('🔗 Ссылки:');
        $this->line('  • Профиль: http://127.0.0.1:8000/admin/profile');
        $this->line('  • Редактирование: http://127.0.0.1:8000/admin/profile/edit');
        $this->line('  • Смена пароля: http://127.0.0.1:8000/admin/profile/change-password');
    }

    /**
     * Показать все профили
     */
    protected function showAllProfiles()
    {
        $users = User::with('department')->get();

        $this->info('👥 Все пользователи системы (' . $users->count() . ')');
        $this->newLine();

        $tableData = [];
        foreach ($users as $user) {
            $tableData[] = [
                $user->id,
                $user->name,
                $user->email,
                $this->getRoleName($user->role),
                $user->department->name ?? 'Не назначен',
                $user->created_at->format('d.m.Y'),
                $user->avatar ? '✅' : '❌'
            ];
        }

        $this->table(
            ['ID', 'Имя', 'Email', 'Роль', 'Отдел', 'Дата регистрации', 'Аватар'],
            $tableData
        );

        $this->newLine();
        $this->info('🔗 Быстрый доступ к профилям:');
        foreach ($users as $user) {
            $this->line('  • ' . $user->name . ': http://127.0.0.1:8000/admin/profile/user/' . $user->id);
        }
    }

    /**
     * Получить название роли
     */
    protected function getRoleName($role)
    {
        return match($role) {
            'admin' => '👑 Администратор',
            'manager' => '👨‍💼 Менеджер',
            'employee' => '👷 Сотрудник',
            default => $role
        };
    }
}
