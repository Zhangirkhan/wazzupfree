<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Position;
use Illuminate\Support\Str;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            [
                'name' => 'Администратор',
                'description' => 'Полный доступ к системе',
                'permissions' => [
                    'users.view', 'users.create', 'users.edit', 'users.delete',
                    'organizations.view', 'organizations.create', 'organizations.edit', 'organizations.delete',
                    'departments.view', 'departments.create', 'departments.edit', 'departments.delete',
                    'positions.view', 'positions.create', 'positions.edit', 'positions.delete',
                    'chats.view', 'chats.create', 'chats.edit', 'chats.delete',
                    'settings.view', 'settings.edit'
                ],
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Менеджер',
                'description' => 'Управление пользователями и организациями',
                'permissions' => [
                    'users.view', 'users.create', 'users.edit',
                    'organizations.view', 'organizations.create', 'organizations.edit',
                    'departments.view', 'departments.create', 'departments.edit',
                    'positions.view', 'positions.create', 'positions.edit',
                    'chats.view', 'chats.create', 'chats.edit',
                    'settings.view'
                ],
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Оператор',
                'description' => 'Работа с чатами и базовые операции',
                'permissions' => [
                    'users.view',
                    'organizations.view',
                    'departments.view',
                    'positions.view',
                    'chats.view', 'chats.create', 'chats.edit'
                ],
                'sort_order' => 3,
                'is_active' => true
            ],
            [
                'name' => 'Модератор',
                'description' => 'Модерация чатов и пользователей',
                'permissions' => [
                    'users.view', 'users.edit',
                    'organizations.view',
                    'departments.view',
                    'positions.view',
                    'chats.view', 'chats.create', 'chats.edit', 'chats.delete'
                ],
                'sort_order' => 4,
                'is_active' => true
            ],
            [
                'name' => 'Пользователь',
                'description' => 'Базовый доступ к системе',
                'permissions' => [
                    'users.view',
                    'organizations.view',
                    'departments.view',
                    'positions.view',
                    'chats.view', 'chats.create'
                ],
                'sort_order' => 5,
                'is_active' => true
            ]
        ];

        foreach ($positions as $positionData) {
            $positionData['slug'] = Str::slug($positionData['name']);
            Position::create($positionData);
        }
    }
}
