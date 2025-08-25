<x-navigation.app>
    @section('title', 'Создание отдела')
    @section('content')
    <x-slot name="title">Создание отдела</x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Создание отдела</h1>
            <x-base.button href="{{ route('admin.departments.index') }}" variant="secondary">
                ← Назад к списку
            </x-base.button>
        </div>

        <x-base.card>
            <form action="{{ route('admin.departments.store') }}" method="POST" class="space-y-6">
                @csrf
                
                @include('admin.departments._form', [
                    'organizations' => $organizations,
                    'isEdit' => false
                ])

                <div class="flex justify-end space-x-3">
                    <x-base.button href="{{ route('admin.departments.index') }}" variant="secondary">
                        Отмена
                    </x-base.button>
                    <x-base.button type="submit" variant="primary">
                        Создать отдел
                    </x-base.button>
                </div>
            </form>
        </x-base.card>
    </div>
    @endsection
</x-navigation.app>
