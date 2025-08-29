<div class="flex grow flex-col gap-y-5 overflow-y-auto px-6 pb-4">
    <!-- Logo -->
    <div class="flex h-16 shrink-0 items-center">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </div>
            <span class="text-xl font-semibold text-gray-900 dark:text-white">Corporate Chat</span>
        </div>
    </div>

    <!-- Информация о пользователе и отделе -->
    <?php
        $roleColors = [
            'admin' => 'bg-red-600',
            'manager' => 'bg-blue-600', 
            'employee' => 'bg-green-600'
        ];
        $userRole = auth()->user()->role ?? 'employee';
        $bgColor = $roleColors[$userRole] ?? 'bg-gray-600';
    ?>
    
    <?php if(auth()->user()->department): ?>
        <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <div class="w-8 h-8 <?php echo e($bgColor); ?> rounded-full flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                        <?php echo e(auth()->user()->name); ?>

                    </p>
                    <?php if(auth()->user()->role): ?>
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            <?php if(auth()->user()->role === 'admin'): ?> bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                            <?php elseif(auth()->user()->role === 'manager'): ?> bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                            <?php else: ?> bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            <?php endif; ?>">
                            <?php echo e(ucfirst(auth()->user()->role)); ?>

                        </span>
                    <?php endif; ?>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                    <?php echo e(auth()->user()->department->name); ?>

                    <?php if(auth()->user()->position): ?>
                        • <?php echo e(auth()->user()->position); ?>

                    <?php endif; ?>
                </p>
            </div>
        </div>
    <?php else: ?>
        <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <div class="w-8 h-8 <?php echo e($bgColor); ?> rounded-full flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                        <?php echo e(auth()->user()->name); ?>

                    </p>
                    <?php if(auth()->user()->role): ?>
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            <?php if(auth()->user()->role === 'admin'): ?> bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                            <?php elseif(auth()->user()->role === 'manager'): ?> bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                            <?php else: ?> bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            <?php endif; ?>">
                            <?php echo e(ucfirst(auth()->user()->role)); ?>

                        </span>
                    <?php endif; ?>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                    Отдел не назначен
                    <?php if(auth()->user()->position): ?>
                        • <?php echo e(auth()->user()->position); ?>

                    <?php endif; ?>
                </p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Navigation -->
    <nav class="flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <li>
                <ul role="list" class="-mx-2 space-y-1">
                    <!-- Dashboard для всех ролей -->
                    <?php if(auth()->user()->hasPermission('dashboard')): ?>
                        <?php if (isset($component)) { $__componentOriginald6ac465da68ef29c651386dfc143c18b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald6ac465da68ef29c651386dfc143c18b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.navigation.nav-item','data' => ['href' => ''.e(route('admin.dashboard')).'','active' => request()->routeIs('admin.dashboard'),'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('navigation.nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('admin.dashboard')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('admin.dashboard')),'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z']); ?>
                            Dashboard
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald6ac465da68ef29c651386dfc143c18b)): ?>
<?php $attributes = $__attributesOriginald6ac465da68ef29c651386dfc143c18b; ?>
<?php unset($__attributesOriginald6ac465da68ef29c651386dfc143c18b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald6ac465da68ef29c651386dfc143c18b)): ?>
<?php $component = $__componentOriginald6ac465da68ef29c651386dfc143c18b; ?>
<?php unset($__componentOriginald6ac465da68ef29c651386dfc143c18b); ?>
<?php endif; ?>
                    <?php endif; ?>

                    <!-- Админские разделы -->
                    <?php if(auth()->user()->role === 'admin'): ?>
                        <?php if(auth()->user()->hasPermission('organizations')): ?>
                            <?php if (isset($component)) { $__componentOriginald6ac465da68ef29c651386dfc143c18b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald6ac465da68ef29c651386dfc143c18b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.navigation.nav-item','data' => ['href' => ''.e(route('admin.organizations.index')).'','active' => request()->routeIs('admin.organizations.*'),'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('navigation.nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('admin.organizations.index')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('admin.organizations.*')),'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4']); ?>
                                Организации
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald6ac465da68ef29c651386dfc143c18b)): ?>
<?php $attributes = $__attributesOriginald6ac465da68ef29c651386dfc143c18b; ?>
<?php unset($__attributesOriginald6ac465da68ef29c651386dfc143c18b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald6ac465da68ef29c651386dfc143c18b)): ?>
<?php $component = $__componentOriginald6ac465da68ef29c651386dfc143c18b; ?>
<?php unset($__componentOriginald6ac465da68ef29c651386dfc143c18b); ?>
<?php endif; ?>
                        <?php endif; ?>

                        <?php if(auth()->user()->hasPermission('departments')): ?>
                            <?php if (isset($component)) { $__componentOriginald6ac465da68ef29c651386dfc143c18b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald6ac465da68ef29c651386dfc143c18b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.navigation.nav-item','data' => ['href' => ''.e(route('admin.departments.index')).'','active' => request()->routeIs('admin.departments.*'),'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('navigation.nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('admin.departments.index')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('admin.departments.*')),'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4']); ?>
                                Отделы
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald6ac465da68ef29c651386dfc143c18b)): ?>
<?php $attributes = $__attributesOriginald6ac465da68ef29c651386dfc143c18b; ?>
<?php unset($__attributesOriginald6ac465da68ef29c651386dfc143c18b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald6ac465da68ef29c651386dfc143c18b)): ?>
<?php $component = $__componentOriginald6ac465da68ef29c651386dfc143c18b; ?>
<?php unset($__componentOriginald6ac465da68ef29c651386dfc143c18b); ?>
<?php endif; ?>
                        <?php endif; ?>

                        <?php if(auth()->user()->hasPermission('positions')): ?>
                            <?php if (isset($component)) { $__componentOriginald6ac465da68ef29c651386dfc143c18b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald6ac465da68ef29c651386dfc143c18b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.navigation.nav-item','data' => ['href' => ''.e(route('admin.positions.index')).'','active' => request()->routeIs('admin.positions.*'),'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('navigation.nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('admin.positions.index')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('admin.positions.*')),'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z']); ?>
                                Должности
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald6ac465da68ef29c651386dfc143c18b)): ?>
<?php $attributes = $__attributesOriginald6ac465da68ef29c651386dfc143c18b; ?>
<?php unset($__attributesOriginald6ac465da68ef29c651386dfc143c18b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald6ac465da68ef29c651386dfc143c18b)): ?>
<?php $component = $__componentOriginald6ac465da68ef29c651386dfc143c18b; ?>
<?php unset($__componentOriginald6ac465da68ef29c651386dfc143c18b); ?>
<?php endif; ?>
                        <?php endif; ?>

                        <?php if(auth()->user()->hasPermission('users')): ?>
                            <?php if (isset($component)) { $__componentOriginald6ac465da68ef29c651386dfc143c18b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald6ac465da68ef29c651386dfc143c18b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.navigation.nav-item','data' => ['href' => ''.e(route('admin.users.index')).'','active' => request()->routeIs('admin.users.*'),'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('navigation.nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('admin.users.index')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('admin.users.*')),'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z']); ?>
                                Пользователи
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald6ac465da68ef29c651386dfc143c18b)): ?>
<?php $attributes = $__attributesOriginald6ac465da68ef29c651386dfc143c18b; ?>
<?php unset($__attributesOriginald6ac465da68ef29c651386dfc143c18b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald6ac465da68ef29c651386dfc143c18b)): ?>
<?php $component = $__componentOriginald6ac465da68ef29c651386dfc143c18b; ?>
<?php unset($__componentOriginald6ac465da68ef29c651386dfc143c18b); ?>
<?php endif; ?>
                        <?php endif; ?>

                        <?php if(auth()->user()->hasPermission('chats')): ?>
                            <?php if (isset($component)) { $__componentOriginald6ac465da68ef29c651386dfc143c18b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald6ac465da68ef29c651386dfc143c18b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.navigation.nav-item','data' => ['href' => ''.e(route('admin.chats.index')).'','active' => request()->routeIs('admin.chats.*'),'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('navigation.nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('admin.chats.index')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('admin.chats.*')),'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z']); ?>
                                Чаты
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald6ac465da68ef29c651386dfc143c18b)): ?>
<?php $attributes = $__attributesOriginald6ac465da68ef29c651386dfc143c18b; ?>
<?php unset($__attributesOriginald6ac465da68ef29c651386dfc143c18b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald6ac465da68ef29c651386dfc143c18b)): ?>
<?php $component = $__componentOriginald6ac465da68ef29c651386dfc143c18b; ?>
<?php unset($__componentOriginald6ac465da68ef29c651386dfc143c18b); ?>
<?php endif; ?>
                        <?php endif; ?>

                        <?php if(auth()->user()->hasPermission('settings')): ?>
                            <?php if (isset($component)) { $__componentOriginald6ac465da68ef29c651386dfc143c18b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald6ac465da68ef29c651386dfc143c18b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.navigation.nav-item','data' => ['href' => ''.e(route('admin.settings.index')).'','active' => request()->routeIs('admin.settings.*'),'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('navigation.nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('admin.settings.index')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('admin.settings.*')),'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z']); ?>
                                Настройки
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald6ac465da68ef29c651386dfc143c18b)): ?>
<?php $attributes = $__attributesOriginald6ac465da68ef29c651386dfc143c18b; ?>
<?php unset($__attributesOriginald6ac465da68ef29c651386dfc143c18b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald6ac465da68ef29c651386dfc143c18b)): ?>
<?php $component = $__componentOriginald6ac465da68ef29c651386dfc143c18b; ?>
<?php unset($__componentOriginald6ac465da68ef29c651386dfc143c18b); ?>
<?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Клиенты для всех ролей -->
                    <?php if(auth()->user()->hasPermission('clients')): ?>
                        <?php if (isset($component)) { $__componentOriginald6ac465da68ef29c651386dfc143c18b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald6ac465da68ef29c651386dfc143c18b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.navigation.nav-item','data' => ['href' => ''.e(route('admin.clients.index')).'','active' => request()->routeIs('admin.clients.*'),'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('navigation.nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('admin.clients.index')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('admin.clients.*')),'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z']); ?>
                            Клиенты
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald6ac465da68ef29c651386dfc143c18b)): ?>
<?php $attributes = $__attributesOriginald6ac465da68ef29c651386dfc143c18b; ?>
<?php unset($__attributesOriginald6ac465da68ef29c651386dfc143c18b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald6ac465da68ef29c651386dfc143c18b)): ?>
<?php $component = $__componentOriginald6ac465da68ef29c651386dfc143c18b; ?>
<?php unset($__componentOriginald6ac465da68ef29c651386dfc143c18b); ?>
<?php endif; ?>
                    <?php endif; ?>

                    <!-- Чат для всех пользователей -->
                    <?php if (isset($component)) { $__componentOriginald6ac465da68ef29c651386dfc143c18b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald6ac465da68ef29c651386dfc143c18b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.navigation.nav-item','data' => ['href' => ''.e(route('user.chat.index')).'','active' => request()->routeIs('user.chat.*'),'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('navigation.nav-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('user.chat.index')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('user.chat.*')),'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z']); ?>
                        Чат
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald6ac465da68ef29c651386dfc143c18b)): ?>
<?php $attributes = $__attributesOriginald6ac465da68ef29c651386dfc143c18b; ?>
<?php unset($__attributesOriginald6ac465da68ef29c651386dfc143c18b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald6ac465da68ef29c651386dfc143c18b)): ?>
<?php $component = $__componentOriginald6ac465da68ef29c651386dfc143c18b; ?>
<?php unset($__componentOriginald6ac465da68ef29c651386dfc143c18b); ?>
<?php endif; ?>


                </ul>
            </li>


        </ul>
    </nav>
</div>
<?php /**PATH /home/zendarol/akzholpharm/corporate-chat/resources/views/components/navigation/sidebar.blade.php ENDPATH**/ ?>