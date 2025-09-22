<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ContractorResource;
use App\Http\Resources\ClientResource;
use App\Models\Contractor;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ContractorController extends ApiController
{
    /**
     * Получить список контрагентов
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 20);
            $search = $request->get('search');
            $type = $request->get('type');

            $query = Contractor::query();

            if ($search) {
                $query->search($search);
            }

            if ($type) {
                $query->where('type', $type);
            }

            $contractors = $query->withCount('clients')
                ->paginate($perPage);

            return $this->paginatedResponse(
                ContractorResource::collection($contractors),
                'Contractors retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve contractors', $e->getMessage(), 500);
        }
    }

    /**
     * Создать контрагента
     */
    public function store(Request $request): JsonResponse
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|in:legal,individual',
        ];

        // Правила для юридических лиц
        if ($request->type === 'legal') {
            $rules = array_merge($rules, [
                'inn' => 'required|string|max:12|unique:contractors,inn',
                'kpp' => 'nullable|string|max:9',
                'ogrn' => 'nullable|string|max:13',
                'legal_address' => 'nullable|string|max:500',
                'actual_address' => 'nullable|string|max:500',
                'bank_name' => 'nullable|string|max:255',
                'bank_account' => 'nullable|string|max:20',
                'bik' => 'nullable|string|max:9',
            ]);
        }

        // Правила для физических лиц
        if ($request->type === 'individual') {
            $rules = array_merge($rules, [
                'inn' => 'nullable|string|max:12|unique:contractors,inn',
                'passport_series' => 'nullable|string|max:4',
                'passport_number' => 'nullable|string|max:6',
                'passport_issued_by' => 'nullable|string|max:255',
                'passport_issued_date' => 'nullable|date',
                'address' => 'nullable|string|max:500',
            ]);
        }

        // Общие правила
        $rules = array_merge($rules, [
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);

        $request->validate($rules);

        try {
            $contractor = Contractor::create($request->all());

            return $this->successResponse(
                new ContractorResource($contractor),
                'Contractor created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create contractor', $e->getMessage(), 500);
        }
    }

    /**
     * Получить контрагента по ID
     */
    public function show(int $id): JsonResponse
    {
        try {
            $contractor = Contractor::with('clients')->find($id);

            if (!$contractor) {
                return $this->notFoundResponse('Contractor not found');
            }

            return $this->successResponse(
                new ContractorResource($contractor),
                'Contractor retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve contractor', $e->getMessage(), 500);
        }
    }

    /**
     * Обновить контрагента
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $contractor = Contractor::find($id);

        if (!$contractor) {
            return $this->notFoundResponse('Contractor not found');
        }

        $rules = [
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:legal,individual',
        ];

        // Правила для юридических лиц
        if ($request->type === 'legal' || $contractor->type === 'legal') {
            $rules = array_merge($rules, [
                'inn' => 'sometimes|string|max:12|unique:contractors,inn,' . $id,
                'kpp' => 'nullable|string|max:9',
                'ogrn' => 'nullable|string|max:13',
                'legal_address' => 'nullable|string|max:500',
                'actual_address' => 'nullable|string|max:500',
                'bank_name' => 'nullable|string|max:255',
                'bank_account' => 'nullable|string|max:20',
                'bik' => 'nullable|string|max:9',
            ]);
        }

        // Правила для физических лиц
        if ($request->type === 'individual' || $contractor->type === 'individual') {
            $rules = array_merge($rules, [
                'inn' => 'nullable|string|max:12|unique:contractors,inn,' . $id,
                'passport_series' => 'nullable|string|max:4',
                'passport_number' => 'nullable|string|max:6',
                'passport_issued_by' => 'nullable|string|max:255',
                'passport_issued_date' => 'nullable|date',
                'address' => 'nullable|string|max:500',
            ]);
        }

        // Общие правила
        $rules = array_merge($rules, [
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);

        $request->validate($rules);

        try {
            $contractor->update($request->all());

            return $this->successResponse(
                new ContractorResource($contractor->fresh()),
                'Contractor updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update contractor', $e->getMessage(), 500);
        }
    }

    /**
     * Удалить контрагента
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $contractor = Contractor::find($id);

            if (!$contractor) {
                return $this->notFoundResponse('Contractor not found');
            }

            $contractor->delete();

            return $this->successResponse(null, 'Contractor deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete contractor', $e->getMessage(), 500);
        }
    }

    /**
     * Получить клиентов контрагента
     */
    public function clients(int $id): JsonResponse
    {
        try {
            $contractor = Contractor::find($id);

            if (!$contractor) {
                return $this->notFoundResponse('Contractor not found');
            }

            $clients = $contractor->clients()->paginate(20);

            return $this->paginatedResponse(
                ClientResource::collection($clients),
                'Contractor clients retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve contractor clients', $e->getMessage(), 500);
        }
    }

    /**
     * Добавить клиента к контрагенту
     */
    public function addClient(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id'
        ]);

        try {
            $contractor = Contractor::find($id);

            if (!$contractor) {
                return $this->notFoundResponse('Contractor not found');
            }

            $client = Client::find($request->client_id);

            if (!$client) {
                return $this->notFoundResponse('Client not found');
            }

            $client->update(['contractor_id' => $id]);

            return $this->successResponse(
                new ClientResource($client->fresh()),
                'Client added to contractor successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to add client to contractor', $e->getMessage(), 500);
        }
    }

    /**
     * Удалить клиента из контрагента
     */
    public function removeClient(int $contractorId, int $clientId): JsonResponse
    {
        try {
            $client = Client::where('contractor_id', $contractorId)->find($clientId);

            if (!$client) {
                return $this->notFoundResponse('Client not found in this contractor');
            }

            $client->update(['contractor_id' => null]);

            return $this->successResponse(null, 'Client removed from contractor successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to remove client from contractor', $e->getMessage(), 500);
        }
    }
}
