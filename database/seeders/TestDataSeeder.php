<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test organization
        $organization = Organization::create([
            'name' => 'Test Company',
            'slug' => 'test-company',
            'description' => 'Тестовая компания для демонстрации',
            'domain' => 'testcompany.com',
            'is_active' => true,
        ]);

        // Create departments
        $itDept = Department::create([
            'organization_id' => $organization->id,
            'name' => 'IT отдел',
            'slug' => 'it-department',
            'description' => 'Отдел информационных технологий',
            'level' => 0,
            'is_active' => true,
        ]);

        $hrDept = Department::create([
            'organization_id' => $organization->id,
            'name' => 'HR отдел',
            'slug' => 'hr-department',
            'description' => 'Отдел кадров',
            'level' => 0,
            'is_active' => true,
        ]);

        // Create roles
        $adminRole = Role::create([
            'organization_id' => $organization->id,
            'name' => 'Администратор',
            'slug' => 'admin',
            'description' => 'Полные права доступа',
            'level' => 100,
            'permissions' => ['all'],
            'is_active' => true,
        ]);

        $managerRole = Role::create([
            'organization_id' => $organization->id,
            'name' => 'Менеджер',
            'slug' => 'manager',
            'description' => 'Управление отделом',
            'level' => 50,
            'permissions' => ['chat_manage', 'message_hide'],
            'is_active' => true,
        ]);

        $employeeRole = Role::create([
            'organization_id' => $organization->id,
            'name' => 'Сотрудник',
            'slug' => 'employee',
            'description' => 'Обычный сотрудник',
            'level' => 10,
            'permissions' => ['chat_participate', 'message_send'],
            'is_active' => true,
        ]);

        // Create test users
        $admin = User::create([
            'name' => 'Администратор',
            'email' => 'admin@testcompany.com',
            'password' => Hash::make('password'),
            'phone' => '+7 999 123-45-67',
            'position' => 'Главный администратор',
        ]);

        $manager = User::create([
            'name' => 'Менеджер IT',
            'email' => 'manager@testcompany.com',
            'password' => Hash::make('password'),
            'phone' => '+7 999 234-56-78',
            'position' => 'Руководитель IT отдела',
        ]);

        $employee1 = User::create([
            'name' => 'Сотрудник 1',
            'email' => 'employee1@testcompany.com',
            'password' => Hash::make('password'),
            'phone' => '+7 999 345-67-89',
            'position' => 'Разработчик',
        ]);

        $employee2 = User::create([
            'name' => 'Сотрудник 2',
            'email' => 'employee2@testcompany.com',
            'password' => Hash::make('password'),
            'phone' => '+7 999 456-78-90',
            'position' => 'HR специалист',
        ]);

        // Assign users to organization with roles and departments
        $admin->organizations()->attach($organization->id, [
            'department_id' => $itDept->id,
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        $manager->organizations()->attach($organization->id, [
            'department_id' => $itDept->id,
            'role_id' => $managerRole->id,
            'is_active' => true,
        ]);

        $employee1->organizations()->attach($organization->id, [
            'department_id' => $itDept->id,
            'role_id' => $employeeRole->id,
            'is_active' => true,
        ]);

        $employee2->organizations()->attach($organization->id, [
            'department_id' => $hrDept->id,
            'role_id' => $employeeRole->id,
            'is_active' => true,
        ]);

        $this->command->info('Test data created successfully!');
        $this->command->info('Admin: admin@testcompany.com / password');
        $this->command->info('Manager: manager@testcompany.com / password');
        $this->command->info('Employee1: employee1@testcompany.com / password');
        $this->command->info('Employee2: employee2@testcompany.com / password');
    }
}
