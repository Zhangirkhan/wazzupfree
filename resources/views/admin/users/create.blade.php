<x-navigation.app>
    @section('title', 'Создание пользователя')
    @section('content')
    <div class="space-y-6">
        <!-- Page header -->
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Создать пользователя
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Добавление нового пользователя в систему
            </p>
        </div>

        <!-- Form -->
        <x-base.card>
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                @csrf

                @include('admin.users._form', [
                    'organizations' => $organizations,
                    'departments' => $departments,
                    'roles' => $roles,
                    'isEdit' => false
                ])

                <!-- Form actions -->
                <div class="flex justify-end space-x-3">
                    <x-base.button type="button" variant="outline" href="{{ route('admin.users.index') }}">
                        Отмена
                    </x-base.button>
                    <x-base.button type="submit">
                        Создать пользователя
                    </x-base.button>
                </div>
            </form>
        </x-base.card>
    </div>
    @endsection
</x-navigation.app>
