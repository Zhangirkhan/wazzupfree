<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Organization;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Position::withCount('users');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $positions = $query->ordered()->paginate(15);

        return view('admin.positions.index', compact('positions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $organizations = Organization::active()->get();
        $departments = Department::active()->get();
        
        return view('admin.positions.create', compact('organizations', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);

        Position::create($data);

        return redirect()->route('admin.positions.index')
            ->with('success', 'Должность успешно создана');
    }

    /**
     * Display the specified resource.
     */
    public function show(Position $position)
    {
        $position->load(['users.organizations', 'users.departments']);
        
        return view('admin.positions.show', compact('position'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Position $position)
    {
        $organizations = Organization::active()->get();
        $departments = Department::active()->get();
        
        return view('admin.positions.edit', compact('position', 'organizations', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Position $position)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);

        $position->update($data);

        return redirect()->route('admin.positions.index')
            ->with('success', 'Должность успешно обновлена');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position)
    {
        // Проверяем, есть ли пользователи с этой должностью
        if ($position->users()->count() > 0) {
            return redirect()->route('admin.positions.index')
                ->with('error', 'Нельзя удалить должность, на которой есть пользователи');
        }

        $position->delete();

        return redirect()->route('admin.positions.index')
            ->with('success', 'Должность успешно удалена');
    }
}
