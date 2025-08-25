@extends('layouts.admin')

@section('title', 'История передач чата')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Заголовок -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">История передач чата</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                История передач чата "{{ $chat->title }}"
            </p>
        </div>

        <!-- Информация о чате -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Информация о чате</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Название</p>
                    <p class="text-gray-900 dark:text-white">{{ $chat->title }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Телефон клиента</p>
                    <p class="text-gray-900 dark:text-white">{{ $chat->messenger_phone }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Текущий отдел</p>
                    <p class="text-gray-900 dark:text-white">
                        {{ $chat->department ? $chat->department->name : 'Не назначен' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- История передач -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">История передач</h3>
            </div>
            
            @if($history->count() > 0)
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($history as $transfer)
                        <div class="px-6 py-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-900 dark:text-white font-medium">
                                        {{ $transfer->content }}
                                    </p>
                                    @if(isset($transfer->metadata['transfer_reason']) && $transfer->metadata['transfer_reason'])
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            <span class="font-medium">Причина:</span> {{ $transfer->metadata['transfer_reason'] }}
                                        </p>
                                    @endif
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                        {{ $transfer->created_at->format('d.m.Y H:i:s') }}
                                    </p>
                                </div>
                                <div class="ml-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        Передача
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-6 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">История передач пуста</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Этот чат еще не передавался между отделами или менеджерами.
                    </p>
                </div>
            @endif
        </div>

        <!-- Дополнительные действия -->
        <div class="mt-8 flex flex-wrap gap-4">
            <a href="{{ route('admin.chat-transfer.form', $chat->id) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
                Передать чат
            </a>
            
                            <a href="{{ route('user.chat.show', $chat->id) }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md transition duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Вернуться к чату
            </a>
        </div>
    </div>
</div>
@endsection
