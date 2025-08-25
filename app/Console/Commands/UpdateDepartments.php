<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Department;
use App\Models\Organization;

class UpdateDepartments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'departments:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновляет отделы согласно списку из тестового меню';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Обновление отделов...');

        // Получаем или создаем организацию
        $organization = Organization::first();
        if (!$organization) {
            $organization = Organization::create([
                'name' => 'Акжол Фарм',
                'description' => 'Основная организация'
            ]);
            $this->info('Создана организация: ' . $organization->name);
        }

        // Список отделов согласно тестовому меню
        $departments = [
            [
                'id' => 1,
                'name' => 'Бухгалтерия',
                'description' => 'Вопросы по бухгалтерии и финансам'
            ],
            [
                'id' => 2,
                'name' => 'IT отдел',
                'description' => 'Техническая поддержка и IT вопросы'
            ],
            [
                'id' => 3,
                'name' => 'HR отдел',
                'description' => 'Вопросы по кадрам и персоналу'
            ],
            [
                'id' => 4,
                'name' => 'Вопросы по товарам в аптеке',
                'description' => 'Вопросы по наличию и ассортименту товаров'
            ]
        ];

        foreach ($departments as $deptData) {
            $department = Department::find($deptData['id']);
            
            if ($department) {
                // Обновляем существующий отдел
                $department->update([
                    'name' => $deptData['name'],
                    'description' => $deptData['description'],
                    'organization_id' => $organization->id
                ]);
                $this->info("Обновлен отдел: {$deptData['name']}");
            } else {
                // Создаем новый отдел
                Department::create([
                    'id' => $deptData['id'],
                    'name' => $deptData['name'],
                    'description' => $deptData['description'],
                    'organization_id' => $organization->id,
                    'slug' => \Str::slug($deptData['name'])
                ]);
                $this->info("Создан отдел: {$deptData['name']}");
            }
        }

        // Удаляем лишние отделы (если есть)
        $existingDepartments = Department::all();
        foreach ($existingDepartments as $dept) {
            $found = false;
            foreach ($departments as $deptData) {
                if ($dept->id == $deptData['id']) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $this->warn("Удален лишний отдел: {$dept->name}");
                $dept->delete();
            }
        }

        $this->info('Отделы обновлены успешно!');
        $this->info('Всего отделов: ' . Department::count());
        
        return 0;
    }
}
