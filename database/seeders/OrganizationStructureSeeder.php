<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Organization;
use App\Models\Department;
use App\Models\Position;
use App\Models\UserPosition;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OrganizationStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Очищаем существующие данные
        $this->command->info('🗑️ Очистка существующих данных...');

        UserPosition::truncate();
        Department::truncate();
        Position::truncate();
        Organization::truncate();
        User::whereNotIn('email', ['admin@admin.com'])->delete(); // Сохраняем админа если есть

        // 2. Создаем должности
        $this->command->info('📋 Создание должностей...');

        $managerPosition = Position::create([
            'name' => 'Менеджер',
            'slug' => 'manager',
            'description' => 'Менеджер организации',
            'permissions' => ['read', 'write', 'manage_clients'],
            'is_active' => true,
            'sort_order' => 2
        ]);

        $directorPosition = Position::create([
            'name' => 'Руководитель',
            'slug' => 'director',
            'description' => 'Руководитель отдела',
            'permissions' => ['read', 'write', 'manage_clients', 'manage_employees', 'view_reports'],
            'is_active' => true,
            'sort_order' => 1
        ]);

        $this->command->info("✅ Должности созданы: {$managerPosition->name}, {$directorPosition->name}");

        // 3. Создаем организацию
        $this->command->info('🏢 Создание организации...');

        $organization = Organization::create([
            'name' => 'Акжол Супермаркет',
            'slug' => Str::slug('Акжол Супермаркет'),
            'description' => 'Супермаркет продуктов и товаров повседневного спроса',
            'domain' => 'akzhol-supermarket.kz',
            'phone' => '+7 (727) 123-45-67',
            'is_active' => true,
        ]);

        $this->command->info("✅ Организация создана: {$organization->name}");

        // 4. Создаем отделы
        $this->command->info('🏬 Создание отделов...');

        $accountingDept = Department::create([
            'organization_id' => $organization->id,
            'name' => 'Бухгалтерия',
            'slug' => Str::slug('Бухгалтерия'),
            'description' => 'Отдел ведения бухгалтерского учета',
            'level' => 1,
            'is_active' => true,
        ]);

        $housekeepingDept = Department::create([
            'organization_id' => $organization->id,
            'name' => 'Хоз отдел',
            'slug' => Str::slug('Хоз отдел'),
            'description' => 'Хозяйственный отдел',
            'level' => 1,
            'is_active' => true,
        ]);

        $this->command->info("✅ Отделы созданы: {$accountingDept->name}, {$housekeepingDept->name}");

        // 5. Создаем 4 сотрудников
        $this->command->info('👥 Создание сотрудников...');

        // Руководитель бухгалтерии
        $accountingDirector = User::create([
            'name' => 'Алия Нурланова',
            'email' => 'aliya.nurlanova@akzhol-supermarket.kz',
            'password' => Hash::make('password123'),
            'phone' => '+7 (701) 234-56-78',
            'role' => 'manager',
            'department_id' => $accountingDept->id,
            'is_active' => true,
        ]);

        // Бухгалтер 1
        $accountant1 = User::create([
            'name' => 'Жанар Касымова',
            'email' => 'zhanar.kasymova@akzhol-supermarket.kz',
            'password' => Hash::make('password123'),
            'phone' => '+7 (701) 345-67-89',
            'role' => 'employee',
            'department_id' => $accountingDept->id,
            'is_active' => true,
        ]);

        // Бухгалтер 2
        $accountant2 = User::create([
            'name' => 'Мадина Сериккызы',
            'email' => 'madina.serikkyzy@akzhol-supermarket.kz',
            'password' => Hash::make('password123'),
            'phone' => '+7 (701) 456-78-90',
            'role' => 'employee',
            'department_id' => $accountingDept->id,
            'is_active' => true,
        ]);

        // Менеджер хоз отдела
        $housekeepingManager = User::create([
            'name' => 'Ерлан Токаев',
            'email' => 'erlan.tokaev@akzhol-supermarket.kz',
            'password' => Hash::make('password123'),
            'phone' => '+7 (701) 567-89-01',
            'role' => 'employee',
            'department_id' => $housekeepingDept->id,
            'is_active' => true,
        ]);

        $this->command->info("✅ Сотрудники созданы:");
        $this->command->info("   📊 Бухгалтерия: {$accountingDirector->name} (руководитель), {$accountant1->name}, {$accountant2->name}");
        $this->command->info("   🏪 Хоз отдел: {$housekeepingManager->name} (менеджер)");

        // 6. Назначаем должности сотрудникам
        $this->command->info('💼 Назначение должностей...');

        // Руководитель бухгалтерии - должность "Руководитель"
        UserPosition::create([
            'user_id' => $accountingDirector->id,
            'position_id' => $directorPosition->id,
            'organization_id' => $organization->id,
            'department_id' => $accountingDept->id,
            'is_primary' => true,
            'assigned_at' => now(),
        ]);

        // Менеджер хоз отдела - должность "Менеджер"
        UserPosition::create([
            'user_id' => $housekeepingManager->id,
            'position_id' => $managerPosition->id,
            'organization_id' => $organization->id,
            'department_id' => $housekeepingDept->id,
            'is_primary' => true,
            'assigned_at' => now(),
        ]);

        $this->command->info("✅ Должности назначены:");
        $this->command->info("   👨‍💼 {$accountingDirector->name} → {$directorPosition->name} в {$accountingDept->name}");
        $this->command->info("   👩‍💼 {$housekeepingManager->name} → {$managerPosition->name} в {$housekeepingDept->name}");

        // 7. Итоговая сводка
        $this->command->info('');
        $this->command->info('🎉 Структура организации успешно создана!');
        $this->command->info('');
        $this->command->info("🏢 Организация: {$organization->name}");
        $this->command->info('');
        $this->command->info('🏬 Отделы:');
        $this->command->info("   📊 {$accountingDept->name} (3 сотрудника)");
        $this->command->info("   🏪 {$housekeepingDept->name} (1 сотрудник)");
        $this->command->info('');
        $this->command->info('📋 Должности:');
        $this->command->info("   👑 {$directorPosition->name}");
        $this->command->info("   💼 {$managerPosition->name}");
        $this->command->info('');
        $this->command->info('👥 Сотрудники:');
        $this->command->info("   📊 Бухгалтерия:");
        $this->command->info("      👑 {$accountingDirector->name} ({$directorPosition->name}) - {$accountingDirector->email}");
        $this->command->info("      👤 {$accountant1->name} (Сотрудник) - {$accountant1->email}");
        $this->command->info("      👤 {$accountant2->name} (Сотрудник) - {$accountant2->email}");
        $this->command->info("   🏪 Хоз отдел:");
        $this->command->info("      💼 {$housekeepingManager->name} ({$managerPosition->name}) - {$housekeepingManager->email}");
        $this->command->info('');
        $this->command->info('🔐 Все пароли: password123');
    }
}
