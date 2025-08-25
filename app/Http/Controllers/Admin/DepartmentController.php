<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Organization;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::with(['organization', 'parent', 'children']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('organization')) {
            $query->where('organization_id', $request->organization);
        }

        $departments = $query->latest()->paginate(15);
        $organizations = Organization::all();

        return view('admin.departments.index', compact('departments', 'organizations'));
    }

    public function create()
    {
        $organizations = Organization::all();
        $departments = Department::all();

        return view('admin.departments.create', compact('organizations', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:departments',
            'description' => 'nullable|string',
            'organization_id' => 'required|exists:organizations,id',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = \Str::slug($data['name']);
        }

        Department::create($data);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Отдел успешно создан');
    }

    public function show(Department $department)
    {
        $department->load(['organization', 'users', 'children']);
        return view('admin.departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        $organizations = Organization::all();
        $departments = Department::where('id', '!=', $department->id)->get();

        return view('admin.departments.edit', compact('department', 'organizations', 'departments'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:departments,slug,' . $department->id,
            'description' => 'nullable|string',
            'organization_id' => 'required|exists:organizations,id',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = \Str::slug($data['name']);
        }

        $department->update($data);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Отдел успешно обновлен');
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Отдел успешно удален');
    }
}
