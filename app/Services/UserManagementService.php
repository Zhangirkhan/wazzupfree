<?php

namespace App\Services;

use App\Models\User;
use App\Models\Department;
use App\Models\Organization;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\UnitOfWorkInterface;
use App\Events\UserCreated;
use App\Exceptions\BusinessLogicException;
use App\Exceptions\ResourceNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserManagementService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UnitOfWorkInterface $unitOfWork
    ) {}

    public function getUsers(int $perPage = 20, ?string $search = null, ?string $role = null, ?int $departmentId = null, ?int $organizationId = null): LengthAwarePaginator
    {
        if ($search) {
            return $this->userRepository->search($search, $perPage);
        }

        if ($role) {
            return $this->userRepository->getByRole($role);
        }

        if ($departmentId) {
            return $this->userRepository->getByDepartment($departmentId);
        }

        if ($organizationId) {
            return $this->userRepository->getByOrganization($organizationId);
        }

        return $this->userRepository->getAll($perPage);
    }

    public function getUser(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function createUser(array $data): User
    {
        return $this->unitOfWork->execute(function ($uow) use ($data) {
            // Проверяем существование отдела, если указан
            if (isset($data['department_id'])) {
                $department = Department::find($data['department_id']);
                if (!$department) {
                    throw new ResourceNotFoundException('Department', $data['department_id']);
                }
            }

            // Проверяем уникальность email
            if ($this->userRepository->findByEmail($data['email'])) {
                throw new BusinessLogicException(
                    'User with this email already exists',
                    'EMAIL_ALREADY_EXISTS',
                    ['email' => $data['email']]
                );
            }

            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'position' => $data['position'] ?? null,
                'role' => $data['role'] ?? 'user',
                'department_id' => $data['department_id'] ?? null,
                'email_verified_at' => now()
            ];

            $user = $this->userRepository->create($userData);

            // Привязываем к организациям, если указаны
            if (isset($data['organization_ids']) && is_array($data['organization_ids'])) {
                foreach ($data['organization_ids'] as $orgId) {
                    $organization = Organization::find($orgId);
                    if (!$organization) {
                        throw new ResourceNotFoundException('Organization', $orgId);
                    }
                }
                $user->organizations()->attach($data['organization_ids']);
            }

            // Добавляем операцию отправки события
            $uow->addOperation(function () use ($user) {
                event(new UserCreated($user));
            });

            Log::info('User created', [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]);

            return $user->load(['department', 'organizations']);
        });
    }

    public function updateUser(int $id, array $data): User
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            throw new ResourceNotFoundException('User', $id);
        }

        return $this->unitOfWork->execute(function ($uow) use ($user, $data) {
            // Проверяем существование отдела, если указан
            if (isset($data['department_id'])) {
                $department = Department::find($data['department_id']);
                if (!$department) {
                    throw new ResourceNotFoundException('Department', $data['department_id']);
                }
            }

            $updateData = array_filter([
                'name' => $data['name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'position' => $data['position'] ?? null,
                'role' => $data['role'] ?? null,
                'department_id' => $data['department_id'] ?? null,
            ], function($value) {
                return $value !== null;
            });

            $user = $this->userRepository->update($user, $updateData);

            // Обновляем привязку к организациям, если указаны
            if (isset($data['organization_ids']) && is_array($data['organization_ids'])) {
                foreach ($data['organization_ids'] as $orgId) {
                    $organization = Organization::find($orgId);
                    if (!$organization) {
                        throw new ResourceNotFoundException('Organization', $orgId);
                    }
                }
                $user->organizations()->sync($data['organization_ids']);
            }

            Log::info('User updated', [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]);

            return $user->load(['department', 'organizations']);
        });
    }

    public function changeUserPassword(int $id, string $newPassword): User
    {
        $user = User::findOrFail($id);

        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        Log::info('User password changed', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return $user;
    }

    public function deactivateUser(int $id): User
    {
        $user = User::findOrFail($id);

        $user->update(['is_active' => false]);

        Log::info('User deactivated', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return $user;
    }

    public function activateUser(int $id): User
    {
        $user = User::findOrFail($id);

        $user->update(['is_active' => true]);

        Log::info('User activated', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return $user;
    }

    public function deleteUser(int $id): bool
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            throw new ResourceNotFoundException('User', $id);
        }

        return $this->unitOfWork->execute(function ($uow) use ($user, $id) {
            // Проверяем, есть ли активные чаты
            if ($user->chats()->count() > 0 || $user->assignedChats()->count() > 0) {
                throw new BusinessLogicException(
                    'Cannot delete user with active chats',
                    'USER_HAS_ACTIVE_CHATS',
                    [
                        'user_id' => $id,
                        'chats_count' => $user->chats()->count(),
                        'assigned_chats_count' => $user->assignedChats()->count()
                    ]
                );
            }

            $result = $this->userRepository->delete($user);

            Log::info('User deleted', [
                'user_id' => $id
            ]);

            return $result;
        });
    }

    public function getRoles(): array
    {
        return [
            [
                'id' => 'admin',
                'name' => 'admin',
                'display_name' => 'Администратор',
                'permissions' => ['dashboard', 'users', 'departments', 'chats', 'organizations', 'positions', 'clients', 'settings']
            ],
            [
                'id' => 'manager',
                'name' => 'manager',
                'display_name' => 'Руководитель',
                'permissions' => ['dashboard', 'clients', 'messenger', 'reports']
            ],
            [
                'id' => 'employee',
                'name' => 'employee',
                'display_name' => 'Менеджер',
                'permissions' => ['dashboard', 'clients', 'messenger']
            ],
            [
                'id' => 'user',
                'name' => 'user',
                'display_name' => 'Пользователь',
                'permissions' => ['dashboard', 'chats', 'messenger']
            ]
        ];
    }

    public function getRole(string $id): ?array
    {
        $roles = $this->getRoles();

        foreach ($roles as $role) {
            if ($role['id'] === $id || $role['name'] === $id) {
                return $role;
            }
        }

        return null;
    }

    /**
     * Получить список всех доступных разрешений
     */
    public function getPermissions(): array
    {
        return [
            'dashboard' => 'Панель управления',
            'users' => 'Управление пользователями',
            'departments' => 'Управление отделами',
            'chats' => 'Управление чатами',
            'organizations' => 'Управление организациями',
            'positions' => 'Управление должностями',
            'clients' => 'Управление клиентами',
            'settings' => 'Настройки системы',
            'messenger' => 'Мессенджер',
            'reports' => 'Отчеты'
        ];
    }

    /**
     * Создать новую роль
     */
    public function createRole(array $data): array
    {
        // В реальном приложении здесь была бы работа с базой данных
        // Пока возвращаем созданную роль
        return [
            'id' => uniqid(),
            'name' => $data['name'],
            'display_name' => $data['display_name'],
            'permissions' => $data['permissions'],
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString()
        ];
    }

    /**
     * Обновить роль
     */
    public function updateRole(string $id, array $data): array
    {
        // В реальном приложении здесь была бы работа с базой данных
        // Пока возвращаем обновленную роль
        return [
            'id' => $id,
            'name' => $data['name'] ?? 'updated_role',
            'display_name' => $data['display_name'] ?? 'Обновленная роль',
            'permissions' => $data['permissions'] ?? ['dashboard'],
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString()
        ];
    }

    /**
     * Удалить роль
     */
    public function deleteRole(string $id): bool
    {
        // В реальном приложении здесь была бы работа с базой данных
        // Пока возвращаем true
        return true;
    }
}



