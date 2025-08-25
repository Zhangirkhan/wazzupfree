<x-navigation.app>
    @section('title', 'Редактирование должности')
    @section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Редактирование должности</h1>
            <x-base.button href="{{ route('admin.positions.index') }}" variant="secondary">
                ← Назад к списку
            </x-base.button>
        </div>

        <x-base.card>
            <form action="{{ route('admin.positions.update', $position) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                @include('admin.positions._form', [
                    'position' => $position,
                    'isEdit' => true
                ])

                <div class="flex justify-end space-x-3">
                    <x-base.button href="{{ route('admin.positions.index') }}" variant="secondary">
                        Отмена
                    </x-base.button>
                    <x-base.button type="submit" variant="primary">
                        Сохранить изменения
                    </x-base.button>
                </div>
            </form>
        </x-base.card>
    </div>
    @endsection
</x-navigation.app>
