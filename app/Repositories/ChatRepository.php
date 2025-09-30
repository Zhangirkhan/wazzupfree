<?php

namespace App\Repositories;

use App\Models\Chat;
use App\Models\User;
use App\Contracts\ChatRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ChatRepository implements ChatRepositoryInterface
{
    public function getAll(int $perPage = 20): LengthAwarePaginator
    {
        return Chat::with(['client', 'department', 'assignedTo', 'lastMessage.user'])
            ->where('status', '!=', 'closed') // Исключаем закрытые чаты
            ->orderBy('last_activity_at', 'desc')
            ->paginate($perPage);
    }

    public function getAllIncludingClosed(int $perPage = 20): LengthAwarePaginator
    {
        return Chat::with(['client', 'department', 'assignedTo', 'lastMessage.user'])
            ->orderBy('last_activity_at', 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Chat
    {
        return Chat::with(['client', 'department', 'assignedTo', 'lastMessage.user'])->find($id);
    }

    public function create(array $data, User $user): Chat
    {
        $data['created_by'] = $user->id;
        $data['last_activity_at'] = now();
        
        return Chat::create($data);
    }

    public function update(Chat $chat, array $data): Chat
    {
        $chat->update($data);
        return $chat->fresh(['client', 'department', 'assignedTo', 'lastMessage.user']);
    }

    public function delete(Chat $chat): bool
    {
        return $chat->delete();
    }

    public function getByOrganization(int $organizationId, int $perPage = 20): LengthAwarePaginator
    {
        return Chat::with(['client', 'department', 'assignedTo', 'lastMessage.user'])
            ->whereHas('client', function($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            })
            ->where('status', '!=', 'closed') // Исключаем закрытые чаты
            ->orderBy('last_activity_at', 'desc')
            ->paginate($perPage);
    }

    public function getByDepartment(int $departmentId, int $perPage = 20): LengthAwarePaginator
    {
        return Chat::with(['client', 'department', 'assignedTo', 'lastMessage.user'])
            ->where('department_id', $departmentId)
            ->where('status', '!=', 'closed') // Исключаем закрытые чаты
            ->orderBy('last_activity_at', 'desc')
            ->paginate($perPage);
    }

    public function getByUser(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        return Chat::with(['client', 'department', 'assignedTo', 'lastMessage.user'])
            ->where('assigned_to', $userId)
            ->where('status', '!=', 'closed') // Исключаем закрытые чаты
            ->orderBy('last_activity_at', 'desc')
            ->paginate($perPage);
    }

    public function getByStatus(string $status, int $perPage = 20): LengthAwarePaginator
    {
        return Chat::with(['client', 'department', 'assignedTo', 'lastMessage.user'])
            ->where('status', $status)
            ->orderBy('last_activity_at', 'desc')
            ->paginate($perPage);
    }

    public function getByClient(int $clientId, int $perPage = 20): LengthAwarePaginator
    {
        return Chat::with(['client', 'department', 'assignedTo', 'lastMessage.user'])
            ->where('client_id', $clientId)
            ->where('status', '!=', 'closed') // Исключаем закрытые чаты
            ->orderBy('last_activity_at', 'desc')
            ->paginate($perPage);
    }

    public function getUnassigned(int $perPage = 20): LengthAwarePaginator
    {
        return Chat::with(['client', 'department', 'assignedTo', 'lastMessage.user'])
            ->whereNull('assigned_to')
            ->where('status', '!=', 'closed') // Исключаем закрытые чаты
            ->orderBy('last_activity_at', 'desc')
            ->paginate($perPage);
    }

    public function getActive(int $perPage = 20): LengthAwarePaginator
    {
        return $this->getByStatus('active', $perPage);
    }

    public function getClosed(int $perPage = 20): LengthAwarePaginator
    {
        return $this->getByStatus('closed', $perPage);
    }

    public function search(string $query, int $perPage = 20): LengthAwarePaginator
    {
        return Chat::with(['client', 'department', 'assignedTo', 'lastMessage.user'])
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhereHas('client', function($clientQuery) use ($query) {
                      $clientQuery->where('name', 'like', "%{$query}%")
                                 ->orWhere('phone', 'like', "%{$query}%");
                  });
            })
            ->where('status', '!=', 'closed') // Исключаем закрытые чаты
            ->orderBy('last_activity_at', 'desc')
            ->paginate($perPage);
    }

    public function getWithUnreadMessages(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        return Chat::with(['client', 'department', 'assignedTo', 'lastMessage.user'])
            ->where('assigned_to', $userId)
            ->where('unread_count', '>', 0)
            ->where('status', '!=', 'closed') // Исключаем закрытые чаты
            ->orderBy('last_activity_at', 'desc')
            ->paginate($perPage);
    }

    public function getByMessengerPhone(string $phone): ?Chat
    {
        return Chat::where('messenger_phone', $phone)->first();
    }
}
