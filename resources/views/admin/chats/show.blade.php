<x-navigation.app>
    @section('title', 'Просмотр чата')
    @section('content')
    <div class="space-y-6">
        <!-- Page header -->
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">
                    {{ $chat->title ?: 'Чат без названия' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $chat->organization->name }} • {{ $chat->creator->name }} • {{ $chat->created_at->format('d.m.Y H:i') }}
                </p>
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                <x-base.button variant="outline" href="{{ route('admin.chats.index') }}">
                    Назад к списку
                </x-base.button>
            </div>
        </div>

        <!-- Chat info -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Chat details -->
            <div class="lg:col-span-2">
                <x-base.card title="Информация о чате">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Статус</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                <x-base.badge :variant="$chat->status === 'active' ? 'success' : ($chat->status === 'closed' ? 'danger' : ($chat->status === 'pending' ? 'warning' : ($chat->status === 'rejected' ? 'danger' : 'warning')))">
                                    @switch($chat->status)
                                        @case('active')
                                            Активен
                                            @break
                                        @case('pending')
                                            Новый
                                            @break
                                        @case('closed')
                                            Закрыт
                                            @break
                                        @case('transferred')
                                            Передан
                                            @break
                                        @case('rejected')
                                            Отклонен
                                            @break
                                        @default
                                            {{ $chat->status }}
                                    @endswitch
                                </x-base.badge>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Тип</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $chat->type }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Создатель</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $chat->creator->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Назначен</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $chat->assignedTo ? $chat->assignedTo->name : 'Не назначен' }}
                            </dd>
                        </div>
                        @if($chat->description)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Описание</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $chat->description }}</dd>
                            </div>
                        @endif
                    </div>
                </x-base.card>

                <!-- Messages -->
                <x-base.card title="Сообщения" class="mt-6">
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        @forelse($messages as $message)
                            <div class="flex space-x-3 {{ $message->type === 'system' ? 'bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-lg' : '' }}">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                        <span class="text-sm font-medium text-indigo-600 dark:text-indigo-300">
                                            {{ $message->user ? $message->user->name[0] : 'К' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $message->user ? $message->user->name : 'Клиент' }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $message->created_at->format('H:i') }}</p>
                                        @if($message->direction === 'in')
                                            <x-base.badge variant="info" size="sm">Входящее</x-base.badge>
                                        @elseif($message->direction === 'out')
                                            <x-base.badge variant="success" size="sm">Исходящее</x-base.badge>
                                        @endif
                                        @if($message->type === 'system')
                                            <x-base.badge variant="warning" size="sm">Системное</x-base.badge>
                                        @endif
                                        @if($message->is_hidden)
                                            <x-base.badge variant="danger" size="sm">Скрыто</x-base.badge>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $message->content }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 dark:text-gray-400">Сообщений пока нет</p>
                        @endforelse
                    </div>

                    @if($messages->hasPages())
                        <div class="mt-4">
                            {{ $messages->links() }}
                        </div>
                    @endif

                    <!-- Wazzup24 Message Form -->
                    @if($chat->type === 'wazzup')
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Отправить сообщение через Wazzup24</h4>
                            <x-wazzup24.message-form :chat="$chat" />
                        </div>
                    @endif
                </x-base.card>
            </div>

            <!-- Actions sidebar -->
            <div>
                <!-- Participants -->
                <x-base.card title="Участники" class="mb-6">
                    <div class="space-y-3">
                        @forelse($chat->participants as $participant)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $participant->user->name[0] }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $participant->user->name }}</p>
                                        <x-base.badge :variant="$participant->role === 'admin' ? 'danger' : ($participant->role === 'moderator' ? 'warning' : 'default')" size="sm">
                                            {{ $participant->role }}
                                        </x-base.badge>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">Участников нет</p>
                        @endforelse
                    </div>
                </x-base.card>

                <!-- Actions -->
                @if($chat->status === 'pending')
                    <x-base.card title="Действия с новым чатом">
                        <div class="space-y-3">
                            <!-- Accept chat -->
                            <div id="accept">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Принять чат</h4>
                                <form method="POST" action="{{ route('admin.chats.accept', $chat) }}" class="space-y-2">
                                    @csrf
                                    <select name="assigned_to" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">Назначить себе</option>
                                        @foreach(\App\Models\User::whereHas('organizations', function($q) use ($chat) { $q->where('organization_id', $chat->organization_id); })->get() as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-base.input name="comment" placeholder="Комментарий (необязательно)" />
                                    <x-base.button type="submit" variant="success" size="sm" class="w-full">
                                        Принять чат
                                    </x-base.button>
                                </form>
                            </div>

                            <hr class="my-4">

                            <!-- Reject chat -->
                            <div id="reject">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Отклонить чат</h4>
                                <form method="POST" action="{{ route('admin.chats.reject', $chat) }}" class="space-y-2">
                                    @csrf
                                    <x-base.input name="reason" placeholder="Причина отклонения" required />
                                    <x-base.button type="submit" variant="danger" size="sm" class="w-full">
                                        Отклонить чат
                                    </x-base.button>
                                </form>
                            </div>
                        </div>
                    </x-base.card>
                @elseif($chat->status === 'active')
                    <x-base.card title="Действия">
                        <div class="space-y-3">
                            <!-- Transfer chat -->
                            <div id="transfer">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Передать чат</h4>
                                <form method="POST" action="{{ route('admin.chats.transfer', $chat) }}" class="space-y-2">
                                    @csrf
                                    <select name="new_user_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                        <option value="">Выберите пользователя</option>
                                        @foreach(\App\Models\User::whereHas('organizations', function($q) use ($chat) { $q->where('organization_id', $chat->organization_id); })->get() as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-base.input name="reason" placeholder="Причина передачи (необязательно)" />
                                    <x-base.button type="submit" variant="warning" size="sm" class="w-full">
                                        Передать чат
                                    </x-base.button>
                                </form>
                            </div>

                            <hr class="my-4">

                            <!-- Close chat -->
                            <div id="close">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Закрыть чат</h4>
                                <form method="POST" action="{{ route('admin.chats.close', $chat) }}" class="space-y-2">
                                    @csrf
                                    <x-base.input name="reason" placeholder="Причина закрытия (необязательно)" />
                                    <x-base.button type="submit" variant="danger" size="sm" class="w-full">
                                        Закрыть чат
                                    </x-base.button>
                                </form>
                            </div>
                        </div>
                    </x-base.card>
                @endif
            </div>
        </div>
    </div>
    @endsection
</x-navigation.app>
