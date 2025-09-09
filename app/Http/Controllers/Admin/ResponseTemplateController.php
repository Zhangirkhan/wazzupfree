<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResponseTemplate;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResponseTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Получаем шаблоны для организации пользователя
        $templates = ResponseTemplate::with(['creator', 'organization'])
            ->forOrganization($user->organization_id)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        $categories = ResponseTemplate::CATEGORIES;
        
        return view('admin.response-templates.index', compact('templates', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ResponseTemplate::CATEGORIES;
        return view('admin.response-templates.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
            'category' => 'required|string|in:' . implode(',', array_keys(ResponseTemplate::CATEGORIES)),
            'is_active' => 'boolean'
        ]);

        $user = Auth::user();
        
        ResponseTemplate::create([
            'name' => $request->name,
            'content' => $request->content,
            'category' => $request->category,
            'is_active' => $request->has('is_active'),
            'created_by' => $user->id,
            'organization_id' => $user->organization_id
        ]);

        return redirect()->route('admin.response-templates.index')
            ->with('success', 'Шаблон ответа успешно создан');
    }

    /**
     * Display the specified resource.
     */
    public function show(ResponseTemplate $responseTemplate)
    {
        $this->authorize('view', $responseTemplate);
        
        return view('admin.response-templates.show', compact('responseTemplate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ResponseTemplate $responseTemplate)
    {
        $this->authorize('update', $responseTemplate);
        
        $categories = ResponseTemplate::CATEGORIES;
        return view('admin.response-templates.edit', compact('responseTemplate', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ResponseTemplate $responseTemplate)
    {
        $this->authorize('update', $responseTemplate);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
            'category' => 'required|string|in:' . implode(',', array_keys(ResponseTemplate::CATEGORIES)),
            'is_active' => 'boolean'
        ]);

        $responseTemplate->update([
            'name' => $request->name,
            'content' => $request->content,
            'category' => $request->category,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.response-templates.index')
            ->with('success', 'Шаблон ответа успешно обновлен');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResponseTemplate $responseTemplate)
    {
        $this->authorize('delete', $responseTemplate);
        
        $responseTemplate->delete();

        return redirect()->route('admin.response-templates.index')
            ->with('success', 'Шаблон ответа успешно удален');
    }

    /**
     * API метод для получения шаблонов по категории
     */
    public function getByCategory(Request $request)
    {
        $user = Auth::user();
        $category = $request->input('category', 'general');
        
        $templates = ResponseTemplate::active()
            ->forOrganization($user->organization_id)
            ->byCategory($category)
            ->orderBy('name')
            ->get(['id', 'name', 'content']);
            
        return response()->json($templates);
    }

    /**
     * API метод для увеличения счетчика использований
     */
    public function incrementUsage(ResponseTemplate $responseTemplate)
    {
        $this->authorize('view', $responseTemplate);
        
        $responseTemplate->incrementUsage();
        
        return response()->json(['success' => true]);
    }
}
