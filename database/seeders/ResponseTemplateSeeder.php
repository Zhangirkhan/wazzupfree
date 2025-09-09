<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ResponseTemplate;
use App\Models\User;
use App\Models\Organization;

class ResponseTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем первого админа и его организацию
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->command->error('Администратор не найден. Создайте сначала пользователя с ролью admin.');
            return;
        }

        // Получаем organization_id через отдел пользователя
        $organizationId = null;
        if ($admin->department_id) {
            $department = \App\Models\Department::find($admin->department_id);
            if ($department) {
                $organizationId = $department->organization_id;
            }
        }
        
        // Если не удалось получить organization_id, используем первую организацию
        if (!$organizationId) {
            $organization = \App\Models\Organization::first();
            if ($organization) {
                $organizationId = $organization->id;
            } else {
                $this->command->error('Организация не найдена. Создайте сначала организацию.');
                return;
            }
        }

        $templates = [
            // Приветствия
            [
                'name' => 'Стандартное приветствие',
                'content' => "Здравствуйте, {client_name}! 👋\n\nДобро пожаловать в нашу службу поддержки. Чем могу помочь?",
                'category' => 'greeting',
                'is_active' => true,
            ],
            [
                'name' => 'Приветствие с представлением',
                'content' => "Добрый день, {client_name}! 😊\n\nМеня зовут {department_name}. Готов помочь вам с любыми вопросами.",
                'category' => 'greeting',
                'is_active' => true,
            ],
            
            // Помощь
            [
                'name' => 'Предложение помощи',
                'content' => "Конечно, {client_name}! Я готов помочь вам решить этот вопрос. Расскажите подробнее, что именно вас интересует?",
                'category' => 'help',
                'is_active' => true,
            ],
            [
                'name' => 'Уточнение вопроса',
                'content' => "Понял ваш вопрос, {client_name}. Для более точного ответа мне нужно уточнить несколько деталей. Можете рассказать подробнее?",
                'category' => 'help',
                'is_active' => true,
            ],
            
            // Поддержка
            [
                'name' => 'Подтверждение решения',
                'content' => "Отлично, {client_name}! Я понимаю вашу ситуацию. Давайте вместе найдем оптимальное решение.",
                'category' => 'support',
                'is_active' => true,
            ],
            [
                'name' => 'Обработка запроса',
                'content' => "Спасибо за обращение, {client_name}! Ваш запрос принят в обработку. Мы свяжемся с вами в ближайшее время.",
                'category' => 'support',
                'is_active' => true,
            ],
            
            // Информация
            [
                'name' => 'Информация о компании',
                'content' => "Уважаемый {client_name}, мы работаем с 9:00 до 18:00 по будням. Наши специалисты всегда готовы помочь вам!",
                'category' => 'information',
                'is_active' => true,
            ],
            [
                'name' => 'Контакты',
                'content' => "Для связи с нами:\n📞 Телефон: +7 (XXX) XXX-XX-XX\n📧 Email: support@company.com\n🌐 Сайт: www.company.com",
                'category' => 'information',
                'is_active' => true,
            ],
            
            // Общие
            [
                'name' => 'Благодарность',
                'content' => "Спасибо за обращение, {client_name}! Было приятно помочь вам. Обращайтесь, если возникнут еще вопросы! 😊",
                'category' => 'general',
                'is_active' => true,
            ],
            [
                'name' => 'Прощание',
                'content' => "До свидания, {client_name}! Желаю вам хорошего дня! Если понадобится помощь - обращайтесь! 👋",
                'category' => 'general',
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            ResponseTemplate::create([
                'name' => $template['name'],
                'content' => $template['content'],
                'category' => $template['category'],
                'is_active' => $template['is_active'],
                'created_by' => $admin->id,
                'organization_id' => $organizationId,
                'usage_count' => 0,
            ]);
        }

        $this->command->info('Создано ' . count($templates) . ' шаблонов ответов.');
    }
}
