<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Администратор',
                'description' => 'Полный доступ ко всем разделам системы',
                'organization_id' => 1,
                'slug' => 'admin'
            ],
            [
                'name' => 'manager',
                'display_name' => 'Менеджер',
                'description' => 'Доступ к мессенджеру и клиентам',
                'organization_id' => 1,
                'slug' => 'manager'
            ],
            [
                'name' => 'employee',
                'display_name' => 'Сотрудник',
                'description' => 'Доступ к мессенджеру и клиентам',
                'organization_id' => 1,
                'slug' => 'employee'
            ]
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
