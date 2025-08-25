<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Organization;
use App\Models\Department;
use App\Models\Role;
use App\Models\Chat;
use App\Models\ChatParticipant;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $organization;
    protected $chat;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаем тестовые данные
        $this->organization = Organization::create([
            'name' => 'Test Company',
            'slug' => 'test-company',
            'description' => 'Test organization',
            'is_active' => true,
        ]);

        $department = Department::create([
            'organization_id' => $this->organization->id,
            'name' => 'IT Department',
            'slug' => 'it-department',
            'description' => 'IT department',
            'is_active' => true,
        ]);

        $role = Role::create([
            'organization_id' => $this->organization->id,
            'name' => 'Employee',
            'slug' => 'employee',
            'description' => 'Employee role',
            'level' => 10,
            'permissions' => ['chat_participate', 'message_send'],
            'is_active' => true,
        ]);

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'phone' => '+7 999 123-45-67',
            'position' => 'Developer',
        ]);

        // Привязываем пользователя к организации
        $this->user->organizations()->attach($this->organization->id, [
            'department_id' => $department->id,
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        // Создаем чат
        $this->chat = Chat::create([
            'organization_id' => $this->organization->id,
            'title' => 'Test Chat',
            'type' => 'private',
            'created_by' => $this->user->id,
            'status' => 'active',
        ]);

        // Добавляем пользователя как участника
        ChatParticipant::create([
            'chat_id' => $this->chat->id,
            'user_id' => $this->user->id,
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_user_can_send_message()
    {
        $token = $this->user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/chats/{$this->chat->id}/messages", [
            'content' => 'Hello, this is a test message!',
            'type' => 'text',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message' => ['id', 'content', 'type', 'user_id'],
                'status'
            ]);

        $this->assertDatabaseHas('messages', [
            'chat_id' => $this->chat->id,
            'user_id' => $this->user->id,
            'content' => 'Hello, this is a test message!',
        ]);
    }

    public function test_user_can_get_messages()
    {
        // Создаем несколько сообщений
        Message::create([
            'chat_id' => $this->chat->id,
            'user_id' => $this->user->id,
            'content' => 'First message',
            'type' => 'text',
        ]);

        Message::create([
            'chat_id' => $this->chat->id,
            'user_id' => $this->user->id,
            'content' => 'Second message',
            'type' => 'text',
        ]);

        $token = $this->user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/chats/{$this->chat->id}/messages");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'messages' => [
                    'data' => [
                        '*' => ['id', 'content', 'type', 'user_id']
                    ]
                ]
            ]);
    }

    public function test_user_can_send_system_message()
    {
        $token = $this->user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/chats/{$this->chat->id}/system-message", [
            'content' => 'This is a system message',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message' => ['id', 'content', 'type'],
                'status'
            ]);

        $this->assertDatabaseHas('messages', [
            'chat_id' => $this->chat->id,
            'user_id' => $this->user->id,
            'content' => 'This is a system message',
            'type' => 'system',
        ]);
    }

    public function test_user_can_hide_message()
    {
        // Создаем сообщение от другого пользователя
        $otherUser = User::create([
            'name' => 'Other User',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
        ]);

        $message = Message::create([
            'chat_id' => $this->chat->id,
            'user_id' => $otherUser->id,
            'content' => 'Message to hide',
            'type' => 'text',
        ]);

        // Добавляем другого пользователя как участника с ролью participant
        ChatParticipant::create([
            'chat_id' => $this->chat->id,
            'user_id' => $otherUser->id,
            'role' => 'participant',
            'is_active' => true,
        ]);

        $token = $this->user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/messages/{$message->id}/hide");

        $response->assertStatus(200);

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'is_hidden' => true,
            'hidden_by' => $this->user->id,
        ]);
    }
}
