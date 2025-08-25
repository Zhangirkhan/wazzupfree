<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Показать профиль текущего пользователя
     */
    public function show()
    {
        $user = Auth::user();
        $user->load(['department', 'organization']);
        
        // Статистика пользователя
        $stats = $this->getUserStats($user);
        
        return view('admin.profile.show', compact('user', 'stats'));
    }

    /**
     * Показать форму редактирования профиля
     */
    public function edit()
    {
        $user = Auth::user();
        $departments = Department::all();
        
        return view('admin.profile.edit', compact('user', 'departments'));
    }

    /**
     * Обновить профиль пользователя
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'name', 'email', 'phone', 'position', 'department_id'
        ]);

        // Обработка аватара
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $avatar->getClientOriginalExtension();
            $avatar->storeAs('public/avatars', $filename);
            $data['avatar'] = 'avatars/' . $filename;
        }

        $user->update($data);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Профиль успешно обновлен');
    }

    /**
     * Показать форму изменения пароля
     */
    public function changePassword()
    {
        return view('admin.profile.change-password');
    }

    /**
     * Обновить пароль пользователя
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Пароль успешно изменен');
    }

    /**
     * Показать профиль другого пользователя (для админов)
     */
    public function showUser(User $user)
    {
        // Проверяем права доступа
        if (Auth::user()->role !== 'admin' && Auth::id() !== $user->id) {
            abort(403, 'Недостаточно прав для просмотра этого профиля');
        }

        $user->load(['department', 'organization']);
        $stats = $this->getUserStats($user);
        
        return view('admin.profile.show-user', compact('user', 'stats'));
    }

    /**
     * Редактировать профиль другого пользователя (для админов)
     */
    public function editUser(User $user)
    {
        // Проверяем права доступа
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Недостаточно прав для редактирования профилей');
        }

        $departments = Department::all();
        
        return view('admin.profile.edit-user', compact('user', 'departments'));
    }

    /**
     * Обновить профиль другого пользователя (для админов)
     */
    public function updateUser(Request $request, User $user)
    {
        // Проверяем права доступа
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Недостаточно прав для редактирования профилей');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'role' => 'required|in:admin,manager,employee',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'name', 'email', 'phone', 'position', 'department_id', 'role'
        ]);

        // Обработка аватара
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $avatar->getClientOriginalExtension();
            $avatar->storeAs('public/avatars', $filename);
            $data['avatar'] = 'avatars/' . $filename;
        }

        $user->update($data);

        return redirect()->route('admin.profile.show-user', $user)
            ->with('success', 'Профиль пользователя успешно обновлен');
    }

    /**
     * Получить статистику пользователя
     */
    private function getUserStats(User $user)
    {
        $stats = [
            'total_chats' => 0,
            'active_chats' => 0,
            'total_messages' => 0,
            'completed_chats' => 0,
        ];

        // Если админ - показываем общую статистику
        if ($user->role === 'admin') {
            $stats['total_chats'] = \App\Models\Chat::count();
            $stats['active_chats'] = \App\Models\Chat::where('status', 'active')->count();
            $stats['total_messages'] = \App\Models\Message::count();
            $stats['completed_chats'] = \App\Models\Chat::where('status', 'closed')->count();
        } else {
            // Для менеджеров и сотрудников - только их чаты
            $stats['total_chats'] = \App\Models\Chat::where(function($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('department_id', $user->department_id)
                  ->orWhereHas('messages', function($subQ) use ($user) {
                      $subQ->where('user_id', $user->id);
                  });
            })->count();

            $stats['active_chats'] = \App\Models\Chat::where('status', 'active')
                ->where(function($q) use ($user) {
                    $q->where('assigned_to', $user->id)
                      ->orWhere('department_id', $user->department_id)
                      ->orWhereHas('messages', function($subQ) use ($user) {
                          $subQ->where('user_id', $user->id);
                      });
                })->count();

            $stats['total_messages'] = \App\Models\Message::where('user_id', $user->id)->count();
            
            $stats['completed_chats'] = \App\Models\Chat::where('status', 'closed')
                ->where(function($q) use ($user) {
                    $q->where('assigned_to', $user->id)
                      ->orWhere('department_id', $user->department_id)
                      ->orWhereHas('messages', function($subQ) use ($user) {
                          $subQ->where('user_id', $user->id);
                      });
                })->count();
        }

        return $stats;
    }
}
