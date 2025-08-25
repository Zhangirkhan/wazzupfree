<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $query = Organization::with(['departments', 'roles', 'users']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $organizations = $query->latest()->paginate(15);

        return view('admin.organizations.index', compact('organizations'));
    }

    public function create()
    {
        return view('admin.organizations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:organizations',
            'description' => 'nullable|string',
            'domain' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = \Str::slug($data['name']);
        }

        Organization::create($data);

        return redirect()->route('admin.organizations.index')
            ->with('success', 'Организация успешно создана');
    }

    public function show(Organization $organization)
    {
        $organization->load(['departments', 'users', 'chats']);
        return view('admin.organizations.show', compact('organization'));
    }

    public function edit(Organization $organization)
    {
        return view('admin.organizations.edit', compact('organization'));
    }

    public function update(Request $request, Organization $organization)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:organizations,slug,' . $organization->id,
            'description' => 'nullable|string',
            'domain' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = \Str::slug($data['name']);
        }

        $organization->update($data);

        return redirect()->route('admin.organizations.index')
            ->with('success', 'Организация успешно обновлена');
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();

        return redirect()->route('admin.organizations.index')
            ->with('success', 'Организация успешно удалена');
    }
}
