<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CompanyController extends ApiController
{
    /**
     * Получить список компаний
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 20);
            $search = $request->get('search');

            $query = Company::query();

            if ($search) {
                $query->search($search);
            }

            $companies = $query->withCount('clients')
                ->orderBy('name')
                ->paginate($perPage);

            return $this->paginatedResponse(
                CompanyResource::collection($companies),
                'Companies retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve companies', $e->getMessage(), 500);
        }
    }

    /**
     * Создать компанию
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'inn' => 'nullable|string|max:20',
            'kpp' => 'nullable|string|max:20',
            'ogrn' => 'nullable|string|max:20',
            'legal_address' => 'nullable|string|max:500',
            'actual_address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:50',
            'bik' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        try {
            $company = Company::create($request->all());

            return $this->successResponse(
                ['company' => new CompanyResource($company->loadCount('clients'))],
                'Company created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create company', $e->getMessage(), 500);
        }
    }

    /**
     * Получить компанию по ID
     */
    public function show(int $id): JsonResponse
    {
        try {
            $company = Company::with('clients')->withCount('clients')->find($id);

            if (!$company) {
                return $this->notFoundResponse('Company not found');
            }

            return $this->successResponse(
                ['company' => new CompanyResource($company)],
                'Company retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve company', $e->getMessage(), 500);
        }
    }

    /**
     * Обновить компанию
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $company = Company::find($id);

        if (!$company) {
            return $this->notFoundResponse('Company not found');
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'inn' => 'nullable|string|max:20',
            'kpp' => 'nullable|string|max:20',
            'ogrn' => 'nullable|string|max:20',
            'legal_address' => 'nullable|string|max:500',
            'actual_address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:50',
            'bik' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        try {
            $company->update($request->all());

            return $this->successResponse(
                ['company' => new CompanyResource($company->fresh()->loadCount('clients'))],
                'Company updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update company', $e->getMessage(), 500);
        }
    }

    /**
     * Удалить компанию (мягкое удаление - деактивация)
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $company = Company::find($id);

            if (!$company) {
                return $this->notFoundResponse('Company not found');
            }

            // Мягкое удаление - просто деактивируем
            $company->update(['is_active' => false]);

            return $this->successResponse(null, 'Company deactivated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to deactivate company', $e->getMessage(), 500);
        }
    }

    /**
     * Получить клиентов компании
     */
    public function clients(int $id): JsonResponse
    {
        try {
            $company = Company::with('clients')->find($id);

            if (!$company) {
                return $this->notFoundResponse('Company not found');
            }

            return $this->successResponse(
                $company->clients,
                'Company clients retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve company clients', $e->getMessage(), 500);
        }
    }
}
