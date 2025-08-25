<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Organization;
use App\Models\Department;
use App\Models\Role;
use App\Models\Chat;
use App\Models\ChatParticipant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $organization;
    protected $department;
    protected $role;

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

        $this->department = Department::create([
            'organization_id' => $this->organization->id,
            'name' => 'IT Department',
            'slug' => 'it-department',
            'description' => 'IT department',
            'is_active' => true,
        ]);

        $this->role = Role::create([
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
            'department_id' => $this->department->id,
            'role_id' => $this->role->id,
            'is_active' => true,
        ]);
    }

    public function test_user_can_create_chat()
    {
        $token = $this->user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/chats', [
            'organization_id' => $this->organization->id,
            'title' => 'Test Chat',
            'description' => 'Test chat description',
            'type' => 'private',
            'participant_ids' => [$this->user->id],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'chat' => ['id', 'title', 'description', 'type'],
                'message'
            ]);

        $this->assertDatabaseHas('chats', [
            'title' => 'Test Chat',
            'organization_id' => $this->organization->id,
        ]);
    }

    public function test_user_can_get_chats_list()
    {
        // Создаем чат
        $chat = Chat::create([
            'organization_id' => $this->organization->id,
            'title' => 'Test Chat',
            'type' => 'private',
            'created_by' => $this->user->id,
            'status' => 'active',
        ]);

        // Добавляем пользователя как участника
        ChatParticipant::create([
            'chat_id' => $chat->id,
            'user_id' => $this->user->id,
            'role' => 'admin',
            'is_active' => true,
        ]);

        $token = $this->user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/chats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'chats' => [
                    '*' => ['id', 'title', 'type', 'status']
                ]
            ]);
    }

    public function test_user_can_get_specific_chat()
    {
        $chat = Chat::create([
            'organization_id' => $this->organization->id,
            'title' => 'Test Chat',
            'type' => 'private',
            'created_by' => $this->user->id,
            'status' => 'active',
        ]);

        ChatParticipant::create([
            'chat_id' => $chat->id,
            'user_id' => $this->user->id,
            'role' => 'admin',
            'is_active' => true,
        ]);

        $token = $this->user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/chats/{$chat->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'chat' => ['id', 'title', 'type', 'status']
            ]);
    }

    public function test_user_can_close_chat()
    {
        $chat = Chat::create([
            'organization_id' => $this->organization->id,
            'title' => 'Test Chat',
            'type' => 'private',
            'created_by' => $this->user->id,
            'status' => 'active',
        ]);

        ChatParticipant::create([
            'chat_id' => $chat->id,
            'user_id' => $this->user->id,
            'role' => 'admin',
            'is_active' => true,
        ]);

        $token = $this->user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/chats/{$chat->id}/close", [
            'reason' => 'Test closure reason',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('chats', [
            'id' => $chat->id,
            'status' => 'closed',
        ]);
    }
}
