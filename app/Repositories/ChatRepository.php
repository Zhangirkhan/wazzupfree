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
        return Chat::with(['client', 'department', 'assignedTo', 'messages' => function($query) {
            $query->latest()->limit(1);
        }])
            ->orderBy('last_activity_at', 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Chat
    {
        return Chat::with(['client', 'department', 'assignedTo', 'messages'])->find($id);
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
        return $chat->fresh(['client', 'department', 'assignedTo', 'messages']);
    }

    public function delete(Chat $chat): bool
    {
        return $chat->delete();
    }

    public function getByOrganization(int $organizationId, int $perPage = 20): LengthAwarePaginator
    {
        return Chat::with(['client', 'department', 'assignedTo', 'messages' => function($query) {
            $query->latest()->limit(1);
        }])
            ->whereHas('client', function($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            })
            ->orderBy('last_activity_at', 'desc')
            ->paginate($perPage);
    }

    public function getByDepartment(int $departmentId, int $perPage = 20): LengthAwarePaginator
    {
        return Chat::with(['client', 'department', 'assignedTo', 'messages' => function($query) {
            $query->latest()->limit(1);
        }])
            ->where('department_id', $departmentId)
            ->orderBy('last_activity_at', 'desc')
            ->paginate($perPage);
    }

    public function getByUser(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        return Chat::with(['client', 'department', 'assignedTo', 'messages' => function($query) {
            $query->latest()->limit(1);
        }])
            ->where('assigned_to', $userId)
            ->orderBy('last_activity_at', 'desc')
            ->paginate($perPage);
    }

    public function getByStatus(string $status, int $perPage = 20): LengthAwarePaginator
    {
        return Chat::with(['client', 'department', 'assignedTo', 'messages' => function($query) {
            $query->latest()->limit(1);
        }])
            ->where('status', $status)
            ->orderBy('last_activity_at', 'desc')
            ->paginate($perPage);
    }

    public function getByClient(int $clientId, int $perPage = 20): LengthAwarePaginator
    {
        return Chat::with(['client', 'department', 'assignedTo', 'messages' => function($query) {
            $query->latest()->limit(1);
        }])
            ->where('client_id', $clientId)
            ->orderBy('last_activity_at', 'desc')
            ->paginate($perPage);
    }

    public function getUnassigned(int $perPage = 20): LengthAwarePaginator
    {
        return Chat::with(['client', 'department', 'assignedTo', 'messages' => function($query) {
            $query->latest()->limit(1);
        }])
            ->whereNull('assigned_to')
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
        return Chat::with(['client', 'department', 'assignedTo', 'messages' => function($query) {
            $query->latest()->limit(1);
        }])
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhereHas('client', function($clientQuery) use ($query) {
                      $clientQuery->where('name', 'like', "%{$query}%")
                                 ->orWhere('phone', 'like', "%{$query}%");
                  });
            })
            ->orderBy('last_activity_at', 'desc')
            ->paginate($perPage);
    }

    public function getWithUnreadMessages(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        return Chat::with(['client', 'department', 'assignedTo', 'messages' => function($query) {
            $query->latest()->limit(1);
        }])
            ->where('assigned_to', $userId)
            ->where('unread_count', '>', 0)
            ->orderBy('last_activity_at', 'desc')
            ->paginate($perPage);
    }

    public function getByMessengerPhone(string $phone): ?Chat
    {
        return Chat::where('messenger_phone', $phone)->first();
    }
}
