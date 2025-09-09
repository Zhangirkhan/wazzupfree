<?php if (isset($component)) { $__componentOriginalc113672a4057e9d1a374a45c3d49bb0a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc113672a4057e9d1a374a45c3d49bb0a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.navigation.app','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('navigation.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startSection('title', 'Чат'); ?>
    
    <!-- Подключаем CDN ресурсы -->
    <?php if (isset($component)) { $__componentOriginalca7f3f3b736164a920ea7edf96500e41 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalca7f3f3b736164a920ea7edf96500e41 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chat-cdn','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chat-cdn'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalca7f3f3b736164a920ea7edf96500e41)): ?>
<?php $attributes = $__attributesOriginalca7f3f3b736164a920ea7edf96500e41; ?>
<?php unset($__attributesOriginalca7f3f3b736164a920ea7edf96500e41); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalca7f3f3b736164a920ea7edf96500e41)): ?>
<?php $component = $__componentOriginalca7f3f3b736164a920ea7edf96500e41; ?>
<?php unset($__componentOriginalca7f3f3b736164a920ea7edf96500e41); ?>
<?php endif; ?>
    
    <!-- Подключаем стили чата -->
    <?php if (isset($component)) { $__componentOriginal728da405957b5feb8af23135d47763a4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal728da405957b5feb8af23135d47763a4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chat-styles','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chat-styles'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal728da405957b5feb8af23135d47763a4)): ?>
<?php $attributes = $__attributesOriginal728da405957b5feb8af23135d47763a4; ?>
<?php unset($__attributesOriginal728da405957b5feb8af23135d47763a4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal728da405957b5feb8af23135d47763a4)): ?>
<?php $component = $__componentOriginal728da405957b5feb8af23135d47763a4; ?>
<?php unset($__componentOriginal728da405957b5feb8af23135d47763a4); ?>
<?php endif; ?>
    
    <?php $__env->startSection('content'); ?>
    <div class="w-full" style="height: 700px;">
        <div class="h-full bg-white dark:bg-gray-800">
            <div class="flex h-full">
                <!-- Левая панель с чатами -->
                <?php if (isset($component)) { $__componentOriginalddb160d1fc89d6be9fc83c37dd7e20e5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalddb160d1fc89d6be9fc83c37dd7e20e5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chat-sidebar','data' => ['chatsData' => $chatsData]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chat-sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['chatsData' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($chatsData)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalddb160d1fc89d6be9fc83c37dd7e20e5)): ?>
<?php $attributes = $__attributesOriginalddb160d1fc89d6be9fc83c37dd7e20e5; ?>
<?php unset($__attributesOriginalddb160d1fc89d6be9fc83c37dd7e20e5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalddb160d1fc89d6be9fc83c37dd7e20e5)): ?>
<?php $component = $__componentOriginalddb160d1fc89d6be9fc83c37dd7e20e5; ?>
<?php unset($__componentOriginalddb160d1fc89d6be9fc83c37dd7e20e5); ?>
<?php endif; ?>

                <!-- Основное окно чата -->
                <?php if (isset($component)) { $__componentOriginal65bea97363789a1f7a3d095da2643fc3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal65bea97363789a1f7a3d095da2643fc3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chat-window','data' => ['currentClient' => $currentClient,'currentChat' => $currentChat,'currentMessages' => $currentMessages]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chat-window'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['currentClient' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentClient),'currentChat' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentChat),'currentMessages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentMessages)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal65bea97363789a1f7a3d095da2643fc3)): ?>
<?php $attributes = $__attributesOriginal65bea97363789a1f7a3d095da2643fc3; ?>
<?php unset($__attributesOriginal65bea97363789a1f7a3d095da2643fc3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal65bea97363789a1f7a3d095da2643fc3)): ?>
<?php $component = $__componentOriginal65bea97363789a1f7a3d095da2643fc3; ?>
<?php unset($__componentOriginal65bea97363789a1f7a3d095da2643fc3); ?>
<?php endif; ?>

                <!-- Поле ввода сообщений -->
                <?php if (isset($component)) { $__componentOriginala9646d0226156dfc7e07c21eee04dbf3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala9646d0226156dfc7e07c21eee04dbf3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.message-input','data' => ['currentChat' => $currentChat]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('message-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['currentChat' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentChat)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala9646d0226156dfc7e07c21eee04dbf3)): ?>
<?php $attributes = $__attributesOriginala9646d0226156dfc7e07c21eee04dbf3; ?>
<?php unset($__attributesOriginala9646d0226156dfc7e07c21eee04dbf3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala9646d0226156dfc7e07c21eee04dbf3)): ?>
<?php $component = $__componentOriginala9646d0226156dfc7e07c21eee04dbf3; ?>
<?php unset($__componentOriginala9646d0226156dfc7e07c21eee04dbf3); ?>
<?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Модальные окна -->
    <?php if (isset($component)) { $__componentOriginale44576376b270666a04c25f3c076119b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale44576376b270666a04c25f3c076119b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.add-chat-modal','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('add-chat-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale44576376b270666a04c25f3c076119b)): ?>
<?php $attributes = $__attributesOriginale44576376b270666a04c25f3c076119b; ?>
<?php unset($__attributesOriginale44576376b270666a04c25f3c076119b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale44576376b270666a04c25f3c076119b)): ?>
<?php $component = $__componentOriginale44576376b270666a04c25f3c076119b; ?>
<?php unset($__componentOriginale44576376b270666a04c25f3c076119b); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal55ce4e4baec97a3888c56bf082c112c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal55ce4e4baec97a3888c56bf082c112c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.media-upload-modal','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('media-upload-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal55ce4e4baec97a3888c56bf082c112c3)): ?>
<?php $attributes = $__attributesOriginal55ce4e4baec97a3888c56bf082c112c3; ?>
<?php unset($__attributesOriginal55ce4e4baec97a3888c56bf082c112c3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal55ce4e4baec97a3888c56bf082c112c3)): ?>
<?php $component = $__componentOriginal55ce4e4baec97a3888c56bf082c112c3; ?>
<?php unset($__componentOriginal55ce4e4baec97a3888c56bf082c112c3); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginaleef46f3e7e890e9c457118f43d4ea623 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaleef46f3e7e890e9c457118f43d4ea623 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.emoji-picker','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('emoji-picker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaleef46f3e7e890e9c457118f43d4ea623)): ?>
<?php $attributes = $__attributesOriginaleef46f3e7e890e9c457118f43d4ea623; ?>
<?php unset($__attributesOriginaleef46f3e7e890e9c457118f43d4ea623); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaleef46f3e7e890e9c457118f43d4ea623)): ?>
<?php $component = $__componentOriginaleef46f3e7e890e9c457118f43d4ea623; ?>
<?php unset($__componentOriginaleef46f3e7e890e9c457118f43d4ea623); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginalf10ee05470bf52c18118624318dcbbb1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf10ee05470bf52c18118624318dcbbb1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.templates-picker','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('templates-picker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf10ee05470bf52c18118624318dcbbb1)): ?>
<?php $attributes = $__attributesOriginalf10ee05470bf52c18118624318dcbbb1; ?>
<?php unset($__attributesOriginalf10ee05470bf52c18118624318dcbbb1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf10ee05470bf52c18118624318dcbbb1)): ?>
<?php $component = $__componentOriginalf10ee05470bf52c18118624318dcbbb1; ?>
<?php unset($__componentOriginalf10ee05470bf52c18118624318dcbbb1); ?>
<?php endif; ?>
    
    <!-- Дополнительные модальные окна -->
    <?php if (isset($component)) { $__componentOriginale71b79f490b25472ac0d1c905202c1bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale71b79f490b25472ac0d1c905202c1bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chat-modals','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chat-modals'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale71b79f490b25472ac0d1c905202c1bc)): ?>
<?php $attributes = $__attributesOriginale71b79f490b25472ac0d1c905202c1bc; ?>
<?php unset($__attributesOriginale71b79f490b25472ac0d1c905202c1bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale71b79f490b25472ac0d1c905202c1bc)): ?>
<?php $component = $__componentOriginale71b79f490b25472ac0d1c905202c1bc; ?>
<?php unset($__componentOriginale71b79f490b25472ac0d1c905202c1bc); ?>
<?php endif; ?>

    <!-- Подключаем JavaScript компоненты -->
    <?php if (isset($component)) { $__componentOriginalfc3cf9eb3194a107c5357db83a22cab7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfc3cf9eb3194a107c5357db83a22cab7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chat-scripts','data' => ['currentChat' => $currentChat,'currentMessages' => $currentMessages]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chat-scripts'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['currentChat' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentChat),'currentMessages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentMessages)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfc3cf9eb3194a107c5357db83a22cab7)): ?>
<?php $attributes = $__attributesOriginalfc3cf9eb3194a107c5357db83a22cab7; ?>
<?php unset($__attributesOriginalfc3cf9eb3194a107c5357db83a22cab7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfc3cf9eb3194a107c5357db83a22cab7)): ?>
<?php $component = $__componentOriginalfc3cf9eb3194a107c5357db83a22cab7; ?>
<?php unset($__componentOriginalfc3cf9eb3194a107c5357db83a22cab7); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal98e5c56f79e758261a35a2d08d0c36e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal98e5c56f79e758261a35a2d08d0c36e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chat-functions','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chat-functions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal98e5c56f79e758261a35a2d08d0c36e2)): ?>
<?php $attributes = $__attributesOriginal98e5c56f79e758261a35a2d08d0c36e2; ?>
<?php unset($__attributesOriginal98e5c56f79e758261a35a2d08d0c36e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal98e5c56f79e758261a35a2d08d0c36e2)): ?>
<?php $component = $__componentOriginal98e5c56f79e758261a35a2d08d0c36e2; ?>
<?php unset($__componentOriginal98e5c56f79e758261a35a2d08d0c36e2); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal2456781eeb3b1019cdb87a968e3cbcef = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2456781eeb3b1019cdb87a968e3cbcef = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chat-utils','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chat-utils'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2456781eeb3b1019cdb87a968e3cbcef)): ?>
<?php $attributes = $__attributesOriginal2456781eeb3b1019cdb87a968e3cbcef; ?>
<?php unset($__attributesOriginal2456781eeb3b1019cdb87a968e3cbcef); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2456781eeb3b1019cdb87a968e3cbcef)): ?>
<?php $component = $__componentOriginal2456781eeb3b1019cdb87a968e3cbcef; ?>
<?php unset($__componentOriginal2456781eeb3b1019cdb87a968e3cbcef); ?>
<?php endif; ?>

    <?php $__env->stopSection(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc113672a4057e9d1a374a45c3d49bb0a)): ?>
<?php $attributes = $__attributesOriginalc113672a4057e9d1a374a45c3d49bb0a; ?>
<?php unset($__attributesOriginalc113672a4057e9d1a374a45c3d49bb0a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc113672a4057e9d1a374a45c3d49bb0a)): ?>
<?php $component = $__componentOriginalc113672a4057e9d1a374a45c3d49bb0a; ?>
<?php unset($__componentOriginalc113672a4057e9d1a374a45c3d49bb0a); ?>
<?php endif; ?><?php /**PATH /home/zendarol/akzholpharm/corporate-chat/resources/views/user/chat/index.blade.php ENDPATH**/ ?>