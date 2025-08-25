<x-navigation.app>
    @section('title', 'Создание клиента')
    @section('content')
    <div class="space-y-6">
        <!-- Page header -->
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">
                Создать клиента
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Добавление нового клиента в систему
            </p>
        </div>

        <!-- Form -->
        <x-base.card>
            <form method="POST" action="{{ route('admin.clients.store') }}" class="space-y-6">
                @csrf

                @include('admin.clients._form', [
                    'isEdit' => false
                ])

                <!-- Form actions -->
                <div class="flex justify-end space-x-3">
                    <x-base.button type="button" variant="outline" href="{{ route('admin.clients.index') }}">
                        Отмена
                    </x-base.button>
                    <x-base.button type="submit">
                        Создать клиента
                    </x-base.button>
                </div>
            </form>
        </x-base.card>
    </div>
    @endsection
</x-navigation.app>
