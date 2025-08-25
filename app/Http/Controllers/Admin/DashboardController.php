<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Organization;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Статистика для конкретного пользователя
        $userStats = [
            'my_messages' => Message::where('user_id', $user->id)->count(),
            'my_chats' => Chat::where('assigned_to', $user->id)->count(),
            'my_active_chats' => Chat::where('assigned_to', $user->id)->where('status', 'active')->count(),
            'my_messenger_chats' => Chat::where('assigned_to', $user->id)->where('is_messenger_chat', true)->count(),
        ];

        // Статистика отдела пользователя
        $departmentStats = [];
        if ($user->department) {
            $departmentStats = [
                'department_name' => $user->department->name,
                'department_users' => User::where('department_id', $user->department_id)->count(),
                'department_chats' => Chat::where('department_id', $user->department_id)->count(),
                'department_active_chats' => Chat::where('department_id', $user->department_id)->where('status', 'active')->count(),
                'department_messenger_chats' => Chat::where('department_id', $user->department_id)->where('is_messenger_chat', true)->count(),
                'department_messages' => Message::whereHas('chat', function($q) use ($user) {
                    $q->where('department_id', $user->department_id);
                })->count(),
            ];
        }

        // Недавние чаты пользователя
        $recentChats = Chat::where('assigned_to', $user->id)
            ->with(['organization', 'creator', 'participants.user'])
            ->latest()
            ->take(5)
            ->get();

        // Недавние сообщения пользователя
        $recentMessages = Message::where('user_id', $user->id)
            ->with(['chat', 'user'])
            ->latest()
            ->take(10)
            ->get();

        // Коллеги из отдела
        $colleagues = [];
        if ($user->department) {
            $colleagues = User::where('department_id', $user->department_id)
                ->where('id', '!=', $user->id)
                ->withCount(['messages', 'chats'])
                ->orderBy('messages_count', 'desc')
                ->take(5)
                ->get();
        }

        // Статистика для админов (если нужно)
        $adminStats = [];
        if ($user->role === 'admin') {
            $adminStats = [
                'total_users' => User::count(),
                'total_organizations' => Organization::count(),
                'total_messages' => Message::count(),
                'total_active_chats' => Chat::where('status', 'active')->count(),
                'total_messenger_chats' => Chat::where('is_messenger_chat', true)->count(),
            ];
        }



        return view('admin.dashboard', compact(
            'userStats', 
            'departmentStats', 
            'recentChats', 
            'recentMessages', 
            'colleagues', 
            'adminStats'
        ));
    }
}
