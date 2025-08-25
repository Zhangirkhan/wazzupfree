<x-navigation.app>
    @section('title', 'Создание должности')
    @section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Создание должности</h1>
            <x-base.button href="{{ route('admin.positions.index') }}" variant="secondary">
                ← Назад к списку
            </x-base.button>
        </div>

        <x-base.card>
            <form action="{{ route('admin.positions.store') }}" method="POST" class="space-y-6">
                @csrf
                
                @include('admin.positions._form', [
                    'isEdit' => false
                ])

                <div class="flex justify-end space-x-3">
                    <x-base.button href="{{ route('admin.positions.index') }}" variant="secondary">
                        Отмена
                    </x-base.button>
                    <x-base.button type="submit" variant="primary">
                        Создать должность
                    </x-base.button>
                </div>
            </form>
        </x-base.card>
    </div>
    @endsection
</x-navigation.app>
