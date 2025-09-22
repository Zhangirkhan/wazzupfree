<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\TemplateResource;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TemplateController extends ApiController
{
    /**
     * Получить список шаблонов
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 20);
            $search = $request->get('search');
            $type = $request->get('type');
            $category = $request->get('category');
            $language = $request->get('language');
            $isActive = $request->get('is_active');
            $isSystem = $request->get('is_system');

            $user = Auth::user();
            $query = Template::query();

            // Фильтрация по организации (пользователь видит только свои шаблоны + системные)
            if ($user->organization_id) {
                $query->forOrganization($user->organization_id);
            }

            if ($search) {
                $query->search($search);
            }

            if ($type) {
                $query->ofType($type);
            }

            if ($category) {
                $query->ofCategory($category);
            }

            if ($language) {
                $query->ofLanguage($language);
            }

            if ($isActive !== null) {
                $query->where('is_active', $isActive);
            }

            if ($isSystem !== null) {
                $query->where('is_system', $isSystem);
            }

            $templates = $query->with(['creator', 'organization'])
                ->orderBy('usage_count', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return $this->paginatedResponse(
                TemplateResource::collection($templates),
                'Templates retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve templates', $e->getMessage(), 500);
        }
    }

    /**
     * Создать шаблон
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:message,email,sms,notification',
            'category' => 'required|in:greeting,farewell,support,sales,technical,general',
            'variables' => 'nullable|array',
            'language' => 'required|in:ru,en,kk',
            'is_active' => 'boolean',
            'organization_id' => 'nullable|exists:organizations,id',
        ]);

        try {
            $user = Auth::user();
            $data = $request->all();
            $data['created_by'] = $user->id;

            // Если не указана организация, используем организацию пользователя
            if (!isset($data['organization_id']) && $user->organization_id) {
                $data['organization_id'] = $user->organization_id;
            }

            $template = Template::create($data);

            return $this->successResponse(
                new TemplateResource($template->load(['creator', 'organization'])),
                'Template created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create template', $e->getMessage(), 500);
        }
    }

    /**
     * Получить шаблон по ID
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $query = Template::query();

            // Фильтрация по организации
            if ($user->organization_id) {
                $query->forOrganization($user->organization_id);
            }

            $template = $query->with(['creator', 'organization'])->find($id);

            if (!$template) {
                return $this->notFoundResponse('Template not found');
            }

            return $this->successResponse(
                new TemplateResource($template),
                'Template retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve template', $e->getMessage(), 500);
        }
    }

    /**
     * Обновить шаблон
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        $query = Template::query();

        // Фильтрация по организации
        if ($user->organization_id) {
            $query->forOrganization($user->organization_id);
        }

        $template = $query->find($id);

        if (!$template) {
            return $this->notFoundResponse('Template not found');
        }

        // Системные шаблоны может редактировать только администратор
        if ($template->is_system && !$user->hasRole('admin')) {
            return $this->errorResponse('Cannot edit system templates', 'Access denied', 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'type' => 'sometimes|in:message,email,sms,notification',
            'category' => 'sometimes|in:greeting,farewell,support,sales,technical,general',
            'variables' => 'nullable|array',
            'language' => 'sometimes|in:ru,en,kk',
            'is_active' => 'boolean',
        ]);

        try {
            $template->update($request->all());

            return $this->successResponse(
                new TemplateResource($template->fresh()->load(['creator', 'organization'])),
                'Template updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update template', $e->getMessage(), 500);
        }
    }

    /**
     * Удалить шаблон
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $query = Template::query();

            // Фильтрация по организации
            if ($user->organization_id) {
                $query->forOrganization($user->organization_id);
            }

            $template = $query->find($id);

            if (!$template) {
                return $this->notFoundResponse('Template not found');
            }

            // Системные шаблоны нельзя удалять
            if ($template->is_system) {
                return $this->errorResponse('Cannot delete system templates', 'Access denied', 403);
            }

            $template->delete();

            return $this->successResponse(null, 'Template deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete template', $e->getMessage(), 500);
        }
    }

    /**
     * Получить шаблоны по типу
     */
    public function byType(Request $request, string $type): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 20);
            $user = Auth::user();
            $query = Template::active()->ofType($type);

            // Фильтрация по организации
            if ($user->organization_id) {
                $query->forOrganization($user->organization_id);
            }

            $templates = $query->with(['creator', 'organization'])
                ->orderBy('usage_count', 'desc')
                ->paginate($perPage);

            return $this->paginatedResponse(
                TemplateResource::collection($templates),
                "Templates of type {$type} retrieved successfully"
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve templates by type', $e->getMessage(), 500);
        }
    }

    /**
     * Получить шаблоны по категории
     */
    public function byCategory(Request $request, string $category): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 20);
            $user = Auth::user();
            $query = Template::active()->ofCategory($category);

            // Фильтрация по организации
            if ($user->organization_id) {
                $query->forOrganization($user->organization_id);
            }

            $templates = $query->with(['creator', 'organization'])
                ->orderBy('usage_count', 'desc')
                ->paginate($perPage);

            return $this->paginatedResponse(
                TemplateResource::collection($templates),
                "Templates of category {$category} retrieved successfully"
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve templates by category', $e->getMessage(), 500);
        }
    }

    /**
     * Получить обработанный контент шаблона
     */
    public function process(Request $request, int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $query = Template::query();

            // Фильтрация по организации
            if ($user->organization_id) {
                $query->forOrganization($user->organization_id);
            }

            $template = $query->find($id);

            if (!$template) {
                return $this->notFoundResponse('Template not found');
            }

            $variables = $request->get('variables', []);
            $processedContent = $template->getProcessedContent($variables);

            // Увеличиваем счетчик использования
            $template->incrementUsage();

            return $this->successResponse([
                'template_id' => $template->id,
                'template_name' => $template->name,
                'original_content' => $template->content,
                'processed_content' => $processedContent,
                'variables_used' => $variables,
            ], 'Template processed successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to process template', $e->getMessage(), 500);
        }
    }

    /**
     * Получить статистику шаблонов
     */
    public function stats(): JsonResponse
    {
        try {
            $user = Auth::user();
            $query = Template::query();

            // Фильтрация по организации
            if ($user->organization_id) {
                $query->forOrganization($user->organization_id);
            }

            $stats = [
                'total_templates' => $query->count(),
                'active_templates' => $query->active()->count(),
                'system_templates' => $query->system()->count(),
                'user_templates' => $query->user()->count(),
                'by_type' => $query->selectRaw('type, COUNT(*) as count')
                    ->groupBy('type')
                    ->pluck('count', 'type'),
                'by_category' => $query->selectRaw('category, COUNT(*) as count')
                    ->groupBy('category')
                    ->pluck('count', 'category'),
                'most_used' => $query->orderBy('usage_count', 'desc')
                    ->limit(5)
                    ->get(['id', 'name', 'usage_count']),
            ];

            return $this->successResponse($stats, 'Template statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve template statistics', $e->getMessage(), 500);
        }
    }

    /**
     * Получить доступные типы, категории и языки
     */
    public function options(): JsonResponse
    {
        try {
            $options = [
                'types' => Template::getTypes(),
                'categories' => Template::getCategories(),
                'languages' => Template::getLanguages(),
            ];

            return $this->successResponse($options, 'Template options retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve template options', $e->getMessage(), 500);
        }
    }
}
