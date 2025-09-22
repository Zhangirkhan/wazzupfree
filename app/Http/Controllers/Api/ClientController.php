<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\Contractor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientController extends ApiController
{
    /**
     * Получить список клиентов
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 20);
            $search = $request->get('search');
            $contractorId = $request->get('contractor_id');
            $isIndividual = $request->get('is_individual');

            $query = Client::query();

            if ($search) {
                $query->search($search);
            }

            if ($contractorId) {
                $query->where('contractor_id', $contractorId);
            }

            if ($isIndividual !== null) {
                if ($isIndividual) {
                    $query->withoutContractor();
                } else {
                    $query->withContractor();
                }
            }

            $clients = $query->with(['contractor', 'company'])
                ->paginate($perPage);

            return $this->paginatedResponse(
                ClientResource::collection($clients),
                'Clients retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve clients', $e->getMessage(), 500);
        }
    }

    /**
     * Создать клиента
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'uuid_wazzup' => 'nullable|string',
            'comment' => 'nullable|string',
            'avatar' => 'nullable|url|max:255',
            'contractor_id' => 'nullable|exists:contractors,id',
            'company_id' => 'nullable|exists:companies,id',
            'is_active' => 'boolean',
        ]);

        try {
            $client = Client::create($request->all());

            return $this->successResponse(
                new ClientResource($client->load(['contractor', 'company'])),
                'Client created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create client', $e->getMessage(), 500);
        }
    }

    /**
     * Получить клиента по ID
     */
    public function show(int $id): JsonResponse
    {
        try {
            $client = Client::with(['contractor', 'company'])->find($id);

            if (!$client) {
                return $this->notFoundResponse('Client not found');
            }

            return $this->successResponse(
                new ClientResource($client),
                'Client retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve client', $e->getMessage(), 500);
        }
    }

    /**
     * Обновить клиента
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $client = Client::find($id);

        if (!$client) {
            return $this->notFoundResponse('Client not found');
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'email' => 'nullable|email|max:255',
            'uuid_wazzup' => 'nullable|string',
            'comment' => 'nullable|string',
            'avatar' => 'nullable|url|max:255',
            'contractor_id' => 'nullable|exists:contractors,id',
            'company_id' => 'nullable|exists:companies,id',
            'is_active' => 'boolean',
        ]);

        try {
            $client->update($request->all());

            return $this->successResponse(
                new ClientResource($client->fresh()->load(['contractor', 'company'])),
                'Client updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update client', $e->getMessage(), 500);
        }
    }

    /**
     * Удалить клиента
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $client = Client::find($id);

            if (!$client) {
                return $this->notFoundResponse('Client not found');
            }

            $client->delete();

            return $this->successResponse(null, 'Client deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete client', $e->getMessage(), 500);
        }
    }

    /**
     * Получить клиентов без контрагента (физ.лица)
     */
    public function individuals(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 20);
            $search = $request->get('search');

            $query = Client::withoutContractor();

            if ($search) {
                $query->search($search);
            }

            $clients = $query->paginate($perPage);

            return $this->paginatedResponse(
                ClientResource::collection($clients),
                'Individual clients retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve individual clients', $e->getMessage(), 500);
        }
    }

    /**
     * Получить клиентов с контрагентами (юр.лица)
     */
    public function corporate(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 20);
            $search = $request->get('search');

            $query = Client::withContractor()->with('contractor');

            if ($search) {
                $query->search($search);
            }

            $clients = $query->paginate($perPage);

            return $this->paginatedResponse(
                ClientResource::collection($clients),
                'Corporate clients retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve corporate clients', $e->getMessage(), 500);
        }
    }

    /**
     * Привязать клиента к контрагенту
     */
    public function attachContractor(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'contractor_id' => 'required|exists:contractors,id'
        ]);

        try {
            $client = Client::find($id);

            if (!$client) {
                return $this->notFoundResponse('Client not found');
            }

            $contractor = Contractor::find($request->contractor_id);

            if (!$contractor) {
                return $this->notFoundResponse('Contractor not found');
            }

            $client->update(['contractor_id' => $request->contractor_id]);

            return $this->successResponse(
                new ClientResource($client->fresh()->load('contractor')),
                'Client attached to contractor successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to attach client to contractor', $e->getMessage(), 500);
        }
    }

    /**
     * Отвязать клиента от контрагента
     */
    public function detachContractor(int $id): JsonResponse
    {
        try {
            $client = Client::find($id);

            if (!$client) {
                return $this->notFoundResponse('Client not found');
            }

            $client->update(['contractor_id' => null]);

            return $this->successResponse(
                new ClientResource($client->fresh()),
                'Client detached from contractor successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to detach client from contractor', $e->getMessage(), 500);
        }
    }
}
