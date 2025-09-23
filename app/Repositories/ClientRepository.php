<?php

namespace App\Repositories;

use App\Models\Client;
use App\Contracts\ClientRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClientRepository implements ClientRepositoryInterface
{
    public function getAll(int $perPage = 20): LengthAwarePaginator
    {
        return Client::with(['organization', 'chats', 'company'])
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Client
    {
        return Client::with(['organization', 'chats', 'company'])->find($id);
    }

    public function findByPhone(string $phone): ?Client
    {
        return Client::where('phone', $phone)->first();
    }

    public function create(array $data): Client
    {
        return Client::create($data);
    }

    public function update(Client $client, array $data): Client
    {
        $client->update($data);
        return $client->fresh(['organization', 'chats', 'company']);
    }

    public function delete(Client $client): bool
    {
        return $client->delete();
    }

    public function search(string $query, int $perPage = 20): LengthAwarePaginator
    {
        return Client::with(['organization', 'chats', 'company'])
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getByOrganization(int $organizationId, int $perPage = 20): LengthAwarePaginator
    {
        return Client::with(['organization', 'chats', 'company'])
            ->where('organization_id', $organizationId)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getActive(int $perPage = 20): LengthAwarePaginator
    {
        return Client::with(['organization', 'chats', 'company'])
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getInactive(int $perPage = 20): LengthAwarePaginator
    {
        return Client::with(['organization', 'chats', 'company'])
            ->where('is_active', false)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getWithChats(int $perPage = 20): LengthAwarePaginator
    {
        return Client::with(['organization', 'chats', 'company'])
            ->whereHas('chats')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getWithoutChats(int $perPage = 20): LengthAwarePaginator
    {
        return Client::with(['organization', 'chats', 'company'])
            ->whereDoesntHave('chats')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getByCompany(int $companyId, int $perPage = 20): LengthAwarePaginator
    {
        return Client::with(['organization', 'chats', 'company'])
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->paginate($perPage);
    }
}
