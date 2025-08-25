<x-navigation.app>
    @section('title', 'Создание организации')
    @section('content')
    <x-slot name="title">Создание организации</x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Создание организации</h1>
            <x-base.button href="{{ route('admin.organizations.index') }}" variant="secondary">
                ← Назад к списку
            </x-base.button>
        </div>

        <x-base.card>
            <form action="{{ route('admin.organizations.store') }}" method="POST" class="space-y-6">
                @csrf
                
                @include('admin.organizations._form', [
                    'isEdit' => false
                ])

                <div class="flex justify-end space-x-3">
                    <x-base.button href="{{ route('admin.organizations.index') }}" variant="secondary">
                        Отмена
                    </x-base.button>
                    <x-base.button type="submit" variant="primary">
                        Создать организацию
                    </x-base.button>
                </div>
            </form>
        </x-base.card>
    </div>
    @endsection
</x-navigation.app>
