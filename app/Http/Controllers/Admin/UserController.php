<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Organization;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['organizations', 'departments', 'roles']);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('position', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('organization')) {
            $query->whereHas('organizations', function($q) use ($request) {
                $q->where('organization_id', $request->organization);
            });
        }

        $users = $query->latest()->paginate(15);
        $organizations = Organization::all();

        return view('admin.users.index', compact('users', 'organizations'));
    }

    public function create()
    {
        $organizations = Organization::all();
        $departments = Department::all();
        $roles = Role::all();

        return view('admin.users.create', compact('organizations', 'departments', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'organization_id' => 'required|exists:organizations,id',
            'department_id' => 'nullable|exists:departments,id',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'position' => $request->position,
        ]);

        // Привязываем к организации
        $user->organizations()->attach($request->organization_id, [
            'department_id' => $request->department_id,
            'role_id' => $request->role_id,
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Пользователь успешно создан');
    }

    public function edit(User $user)
    {
        $organizations = Organization::all();
        $departments = Department::all();
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'organizations', 'departments', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'organization_id' => 'required|exists:organizations,id',
            'department_id' => 'nullable|exists:departments,id',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $request->position,
        ]);

        // Обновляем связи
        $user->organizations()->sync([
            $request->organization_id => [
                'department_id' => $request->department_id,
                'role_id' => $request->role_id,
                'is_active' => true,
            ]
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Пользователь успешно обновлен');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Пользователь успешно удален');
    }
}
