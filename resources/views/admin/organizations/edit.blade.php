<x-navigation.app>
    @section('title', 'Редактирование организации')
    @section('content')
    <x-slot name="title">Редактирование организации</x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Редактирование организации</h1>
            <x-base.button href="{{ route('admin.organizations.index') }}" variant="secondary">
                ← Назад к списку
            </x-base.button>
        </div>

        <x-base.card>
            <form action="{{ route('admin.organizations.update', $organization) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                @include('admin.organizations._form', [
                    'organization' => $organization,
                    'isEdit' => true
                ])

                <div class="flex justify-end space-x-3">
                    <x-base.button href="{{ route('admin.organizations.index') }}" variant="secondary">
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
